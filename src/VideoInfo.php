<?php

namescape Mrofi/VideoInfo;

class VideoInfo
{
    const YOUTUBE = 'Youtube';
    const VIMEO = 'vimeo';
    cosnt DAILYMOTION = 'DailyMotion';
    
    public function __contruct($url, $type = null)
    {
        if ($type === null) {
            $all = [static::YOUTUBE, static::VIMEO, static::DAILYMOTION];
            foreach ($all as $type) {
                $func = 'get'.$type.'Id';
                if ($func($type)) {
                    break;
                }
            }
        }
        
        switch $type {
            case static::YOUTUBE:
                $id = $this->getYoutubeId($url);
                return new Youtube($id);
                break;
            
            case static::VIMEO:
                $id = $this->getVimeoId($url);
                return new Vimeo($id);
                break;
                
            case static::DAILYMOTION:
                $id = $this->getDailyMotionId($url);
                return new DailyMotion($id);
                break;
        
            default:
                return false;        
        }
    }
    
    public function getYoutubeId($url)
    {
        $videoId = false;
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
            $videoId = $match[1];
        }
        return $videoId;
    }
    
    
    // Credit by https://github.com/lingtalfi/video-ids-and-thumbnails/blob/master/function.video.php
    /**
    * Extracts the vimeo id from a vimeo url.
    * Returns false if the url is not recognized as a vimeo url.
    */
    public function getVimeoId($url)
    {
        if (preg_match('#(?:https?://)?(?:www.)?(?:player.)?vimeo.com/(?:[a-z]*/)*([0-9]{6,11})[?]?.*#', $url, $m)) {
            return $m[1];
        }
        return false;
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

