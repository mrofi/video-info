<?php

namespace Mrofi\VideoInfo;

interface VideoInfoInterface
{
    public static function getId($url);

    public function getDuration();
    
    public function getThumbnail();
}
