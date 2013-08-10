<?php

namespace core\container;

class Container
{
    private $objects;
    private $singletons;
    private $injections;
    
    public function __construct()
    {
        $this->initialize();
        $this->injections = simplexml_load_file(INJECT_FILE);
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
            $object = $this->objects[$key]();
            $reflection = new \ReflectionObject($object);
            
            foreach ($this->injections as $class)
            {
                if ($class['name'] == $key)
                {
                    foreach ($class->field as $field)
                    {
                        $dependency = $this->create((string) $field['class']);
                        $property = $reflection->getProperty($field['name']);
                        $property->setAccessible(true);
                        $property->setValue($object, $dependency);
                    }
                }
            }
            return $object;
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