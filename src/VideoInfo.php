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
        if ($type === null) {
            $all = [static::YOUTUBE, static::VIMEO];
            foreach ($all as $info) {
                if ($info::getId($url)) {
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
}

