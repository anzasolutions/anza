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
    
    public function create($classname)
    {
        $classname = strtolower($classname);
        
        if (isset($this->objects[$classname]))
        {
            return $this->objects[$classname]();
        }
    }
    
    public function singleton($classname)
    {
        $classname = strtolower($classname);
        
        if (isset($this->singletons[$classname]))
        {
            return $this->singletons[$classname];
        }
        return $this->singletons[$classname] = $this->objects[$classname]();
    }
    
    public function bind($key, $closure)
    {
        $this->objects[$key] = $closure;
    }
}

?>