<?php

namespace Mrofi\VideoInfo;

abstract class AbstractInfo
{
    protected $attributes;
    
    public function __get($property)
    {
        $func = 'get'.ucfirst($property);
        if (!$this->attributes && !method_exists($this, $func)) {
            return null;
        }
            
        if (method_exists($this, $func)) {
            return $this->$func();
        }
        
        return $this->attributes->$property;
    }
}
