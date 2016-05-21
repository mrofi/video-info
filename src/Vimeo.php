<?php

namespace Mrofi\VideoInfo;

use DateInterval;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Mrofi\VideoInfo\VideoInfoInterface as VideoContract;

class Vimeo extends AbstractInfo implements VideoContract
{
    protected static $endpoint = 'http://vimeo.com/api/oembed.json';
    protected $attributes;
    
    public function __construct($id)
    {
        $client = new Client();
        $videoUrl = rawurlencode('http://vimeo.com/'.$id);
        $query = [
            'url' => $videoUrl,
            'width' => '640',
        ];
        try {
            $response = $client->request('GET', static::$endpoint, compact('query'));
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

    // Credit by https://github.com/lingtalfi/video-ids-and-thumbnails/blob/master/function.video.php
    /**
    * Extracts the vimeo id from a vimeo url.
    * Returns false if the url is not recognized as a vimeo url.
    */
    public static function getVimeoId($url)
    {
        if (preg_match('#(?:https?://)?(?:www.)?(?:player.)?vimeo.com/(?:[a-z]*/)*([0-9]{6,11})[?]?.*#', $url, $m)) {
            return $m[1];
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
    
    public function getThumbnail($type = 'default')
    {
        if (!$this->attributes) {
            return null;
        }

        return $this->attributes->thumbnail_url;
    }
}
