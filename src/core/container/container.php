<?php

namespace core\container;

class Container
{
    private $objects = array();
    private $classpathFinder;
    
	public function __construct()
	{
	    $this->classpathFinder = new ClasspathFinder();
	}
	
	public function create($class)
	{
	    $namespace = $this->classpathFinder->find($class);
	    $fullname = $namespace .'\\'. $class;
	    return new $fullname();
	}
}

?>