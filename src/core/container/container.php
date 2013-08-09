<?php

namespace core\container;

class Container
{
    private static $instance;
    
    private $objects = array();
    private $classpathFinder;
    
    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct()
    {
        $this->classpathFinder = new ClasspathFinder();
    }
    
    // or rather get() ?
    public static function create($classname)
    {
        try
        {
            class_exists($classname);
            $bindKey = strtolower(substr(strrchr($classname, '\\'), 1));
        }
        catch (\LogicException $e)
        {
            $container = self::getInstance();
            $namespace = $container->classpathFinder->find($classname);
            if (empty($namespace))
            {
                throw new CreateException();
            }
            $classname = $namespace . '\\' . $classname;
            $bindKey = $classname;
        }
        
        $object = new $classname();
        $container->objects[$bindKey] = $object;
        return $object;
    }
    
    public static function get($classname, $singleton = false)
    {
        $classname = strtolower($classname);
        $container = self::getInstance();
        return $container->classpathFinder->get($classname, $singleton);
//     	if (isset($container->objects[$classname]))
//     	{
//     	    return $container->objects[$classname]();
//     	}
    	throw new NoInstanceInContainerException();
    }
}

?>