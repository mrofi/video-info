<?php

namespace Mrofi\VideoInfo;

use DateInterval;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Mrofi\VideoInfo\VideoInfoInterface as VideoContract;

class DailyMotion extends AbstractInfo implements VideoContract
{
    protected static $endpoint = 'https://api.dailymotion.com/video';
    protected $attributes;
    
    public function __construct($id)
    {
        $client = new Client();
        $query = [
            'fields' => 'duration,thumbnail_url,thumbnail_60_url,thumbnail_120_url,thumbnail_180_url,thumbnail_240_url,thumbnail_360_url,thumbnail_480_url,thumbnail_720_url',
        ];

        try {
            $response = $client->request('GET', static::$endpoint.'/'.$id, compact('query'));
            if ($response->getStatusCode() == '200') {
                $body = $response->getBody();
                $content = $body->getContents();
                $obj = json_decode($content);
                $this->attributes = $obj;
                $this->attributes->id = $id;
            }
        } catch (TransferException $e) {
          //
        }
    }

    /**
    * Extracts the daily motion id from a daily motion url.
    * Returns false if the url is not recognized as a daily motion url.
    */
    public function getDailyMotionId($url)
    {
        if (preg_match('!^.+dailymotion\.com/(video|hub)/([^_]+)[^#]*(#video=([^_&]+))?|(dai\.ly/([^_]+))!', $url, $m)) {
            if (isset($m[6])) {
                return $m[6];
            }
            if (isset($m[4])) {
                return $m[4];
            }
            return $m[2];
        }
        return false;
    }
    
    public function getDuration()
    {
        if (!$this->attributes) {
            return null;
        }

        return $this->attributes->duration;
    }
    
    public function getThumbnail($type = '')
    {
        if (!$this->attributes) {
            return null;
        }

        $thumbnail = $type != '' ? 'thumbnail_'.$type.'_url' : 'thumbnail_url';
        return $this->attributes->$thumbnail;
    }
}
