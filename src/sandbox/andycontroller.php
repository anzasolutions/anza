<?php

namespace sandbox;

class AndyController extends Controller
{
    private $container;
    private $session;
    
    public function getContainerValue($key)
    {
    	return $this->container[$key];
    }
    
    public function display()
    {
//     	echo $this->box->foo;
    }
}

?>