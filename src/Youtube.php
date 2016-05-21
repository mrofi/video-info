<?php

namespace Mrofi/VideoInfo;

use DateInterval;
use GuzzleHttp/Client;
use Mrofi/VideoInfo/VideoInfoInterface as VideoContract;

class Youtube implements VideoContract
{
    protected static $endpoint = 'https://www.googleapis.com/youtube/v3/videos';
    protected static $imageBaseUrl = 'https://i.ytimg.com/vi';
    protected static $apiKey;
    protected $attributes = [];
    
    public function __construct($id)
    {
        $client = new Client();
        $query = [
            'part' => 'contentDetails',
            'key' => static::$apiKey,
            'id' => $id,
        ];
        try {
            $response = $client->re quest('GET', static::$endpoint, compact('query'));
            if ($response->getStatusCode() == '200') {
                $body = $response->getBody();
                $content = $body->getContents();
                $obj = json_decode($content);
                $this->attributes = $obj->items[0]->contentDetails;
                $this->attributes->id = $id;
            }
        } catch (GuzzleHttp\Exception\TransferException $e) {
          //
        }
    }
    
    public static function setApi($apiKey)
    {
        static::$apiKey = $apiKey;
    }
    
    public function getDuration()
    {
        $duration = new DateInterval($this->attributes->duration);
        $only = ['d','h','i','s'];
        return array_intersect_key((array) $duration, array_flip($only))
    }
    
    public function getThumbnail($type = 'default')
    {
        return static::$imageBaseUrl. '/'. $this->attributes->id . '/' .strtolower($type). '.jpg'; 
    }
    
    public function __get($property)
    {
        if (isset($this->attributes->$property)) {
            if (method_exists($this, $func = 'get'.ucfirst($property))) {
                return $this->$func();
            }
            return $this->attributes->property;
        }
        
        return null;
    }
}
