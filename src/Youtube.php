<?php

namespace Mrofi\VideoInfo;

use DateInterval;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Mrofi\VideoInfo\VideoInfoInterface as VideoContract;

class Youtube extends AbstractInfo implements VideoContract
{
    protected static $endpoint = 'https://www.googleapis.com/youtube/v3/videos';
    protected static $imageBaseUrl = 'https://i.ytimg.com/vi';
    protected static $apiKey;
    protected $attributes;
    
    public function __construct($id)
    {
        $client = new Client();
        $query = [
            'part' => 'contentDetails',
            'key' => static::$apiKey,
            'id' => $id,
        ];
        try {
            $response = $client->request('GET', static::$endpoint, compact('query'));
            if ($response->getStatusCode() == '200') {
                $body = $response->getBody();
                $content = $body->getContents();
                $obj = json_decode($content);
                $this->attributes = $obj->items[0]->contentDetails;
                $this->attributes->id = $id;
            }
        } catch (TransferException $e) {
            //
        }
    }
    
    public static function setApi($apiKey)
    {
        static::$apiKey = $apiKey;
    }

    // Credit by https://github.com/lingtalfi/video-ids-and-thumbnails/blob/master/function.video.php
    /**
    * Extracts the vimeo id from a vimeo url.
    * Returns false if the url is not recognized as a vimeo url.
    */
    public static function getYoutubeId($url)
    {
        $videoId = false;
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
            $videoId = $match[1];
        }

        return $videoId;
    }
    
    public function getDuration()
    {
        if (!$this->attributes) {
            return null;
        }

        $interval = new DateInterval($this->attributes->duration);
        return $interval->h * 3600 + $interval->i * 60 + $interval->s;
    }
    
    public function getThumbnail($type = 'default')
    {
        if (!$this->attributes) {
            return null;
        }

        return static::$imageBaseUrl. '/'. $this->attributes->id . '/' .strtolower($type). '.jpg';
    }
}
