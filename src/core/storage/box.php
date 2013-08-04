<?php

namespace core\storage;

// alternative name
// class Silo
class Box
{
    private $content;
    
    public function __construct(array $content = array())
    {
    	$this->content = $content;
    }
    
    public function __get($key)
    {
        if (isset($this->content[$key]))
        {
            return $this->content[$key];
        }
    }
    
    public function __set($key, $value)
    {
        $this->content[$key] = $value;
    }
}

?>