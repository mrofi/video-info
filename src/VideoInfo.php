<?php

namespace Mrofi\VideoInfo;

class VideoInfo
{
    const YOUTUBE = 'Youtube';
    const VIMEO = 'Vimeo';
    const DAILYMOTION = 'DailyMotion';

    protected $obj;
    
    public function __construct($url, $type = null)
    {
        $namespace = '\\Mrofi\\VideoInfo\\';
        if ($type === null) {
            $all = [static::YOUTUBE, static::VIMEO, static::DAILYMOTION];
            foreach ($all as $info) {
                $func = 'get'.$info.'Id';
                $class = $namespace.$info;
                if ($class::$func($url)) {
                    $type = $info;
                    break;
                }
            }
        }

        if ($type === null) {
            return;
        }

        $class = $namespace.$type;
        $id = $class::$func($url);
        $this->obj = new $class($id);
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
