<?php

namespace core\container;

class Container
{
    private $classes;
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
                $class = $namespace . '\\' . $classname;
                $this->classes[$classname] = $class;
                $this->objects[$classname] = function () use($class)
                {
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
            return $this->inject($key, $object);
        }
    }
    
    public function singleton($key)
    {
        if (isset($this->singletons[$key]))
        {
            return $this->singletons[$key];
        }
        $object = $this->objects[$key]();
        return $this->singletons[$key] = $this->inject($key, $object);
    }
    
    public function inject($key, $object)
    {
        $object = $this->objects[$key]();
        $reflection = new \ReflectionObject($object);
        
        foreach ($this->injections as $class)
        {
            if ($class['name'] == $key)
            {
                foreach ($class->field as $field)
                {
                    if ((string) $field['singleton'] == 'true')
                    {
                        $dependency = $this->singleton((string) $field['type']);
                    }
                    else
                    {
                        $dependency = $this->create((string) $field['type']);
                    }
                    
                    $property = $reflection->getProperty($field['name']);
                    
                    if (!empty($field['supertype']))
                    {
                        $super = $this->classes[(string) $field['supertype']];
                        if (!($dependency instanceof $super))
                        {
                            throw new IncorrectInjectionSupertypeException("{$field['type']} is not an instance of $super");
                        }
                    }
                    $property->setAccessible(true);
                    $property->setValue($object, $dependency);
                }
            }
        }
        return $object;
    }
    
    public function bind($key, $closure)
    {
        $this->objects[$key] = $closure;
    }
}

?>