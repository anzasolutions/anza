<?php

namespace sandbox;

class Controller2 extends Controller
{
    private $cont1;
    private $container;
    
    public function __construct()
    {
    	echo 'Controller2';
    }
    
    public function other()
    {
    	echo 'Controller2::other()';
    }
    
    public function some()
    {
    	echo 'Controller2::some()';
    }
    
    public function value($value)
    {
    	echo "Controller2::value($value)";
    }
}

?>