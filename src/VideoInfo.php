<?php

namespace Mrofi\VideoInfo;

class VideoInfo
{
    const YOUTUBE = 'Youtube';
    const VIMEO = 'Vimeo';
    const DAILYMOTION = 'DailyMotion';

    protected $obj;
    
    public function __contruct($url, $type = null)
    {
        Youtube::setApi(env('YOUTUBE_API'));

        if ($type === null) {
            $all = [static::YOUTUBE, static::VIMEO, static::DAILYMOTION];
            foreach ($all as $info) {
                $fun = 'get'.$info.'Id';
                if ($info::$func($url)) {
                    $type = $info;
                    break;
                }
            }
        }

        if ($type === null) {
            return;
        }

        $this->obj = new $type($url);
    }

    public function getVideo()
    {
        return $this->obj;
    }

    public function __call($method, $params)
    {
        return $this->obj ? call_user_func_array(array($this->obj, $method), $params) : $this;
    }

    public function __get($property)
    {
        return $this->obj ? $this->obj->$property : null;
    }
}
