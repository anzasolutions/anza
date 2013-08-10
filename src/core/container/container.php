<?php

namespace core\container;

class Container
{
    private $objects;
    private $singletons;
    
    public function __construct()
    {
        $this->initialize();
    }
    
    private function initialize()
    {
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(APPLICATION_ROOT_PATH));
        foreach ($it as $path)
        {
            if (!$it->isDot())
            {
                $namespace = $it->getSubPath();
                $classname = $path->getBasename('.php');
                $this->objects[$classname] = function () use ($namespace, $classname)
                {
                    $class = $namespace . '\\' . $classname;
                    return new $class();
                };
            }
        }
    }
    
    public function create($key)
    {
        if (isset($this->objects[$key]))
        {
            return $this->objects[$key]();
        }
    }
    
    public function singleton($key)
    {
        if (isset($this->singletons[$key]))
        {
            return $this->singletons[$key];
        }
        return $this->singletons[$key] = $this->objects[$key]();
    }
    
    public function bind($key, $closure)
    {
        $this->objects[$key] = $closure;
    }
}

?>