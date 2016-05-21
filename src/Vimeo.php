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
    
    public function getDuration()
    {
        return isset($this->attributes->duration) ? $this->attributes->duration : null;
    }
    
    public function getThumbnail($type = 'default')
    {
        return isset($this->attributes->thumbnail_url) ? $this->attributes->thumbnail_url : null;
    }
  }
  
