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
                $this->objects[$classname] = function ($singleton = false) use ($namespace, $classname)
                {
                    $class = $namespace . '\\' . $classname;
                    return new $class();
                };
            }
        }
    }
    
    public function get($classname, $singleton = false)
    {
        $classname = strtolower($classname);
        
        if ($singleton)
        {
            if (isset($this->singletons[$classname]))
            {
                return $this->singletons[$classname];
            }
            return $this->singletons[$classname] = $this->objects[$classname]($singleton);
        }
        
        if (isset($this->objects[$classname]))
        {
            return $this->objects[$classname]();
        }
    }
}

?>