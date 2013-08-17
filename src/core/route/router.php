<?php

namespace core\route;

class Router
{
    private $container;
    private $routes;
    private $parts;
    
	public function __construct()
	{
	    $this->routes = simplexml_load_file(ROUTES_FILE);
	    $this->dissect();
	    $this->route();
	}
	
	public function dissect()
	{
		$this->parts = explode('/', trim($_REQUEST['route'], '/'));
	}
	
	public function route()
	{
	    $handlerName = '';
	    $action = '';
	    $xpath = '//routes/';
        
        foreach ($this->parts as $part)
        {
            $xpath .= '/route[@path = "' . $part . '"]';
            $routes = $this->routes->xpath($xpath);
            
            if (empty($routes))
            {
                $xpath = substr_replace($xpath, '{args}', strrpos($xpath, $part), strlen($part));
                $routes = $this->routes->xpath($xpath);
                if (empty($routes))
                {
                    $handlerName = 'errorcontroller'; // TODO to be configured
                    $action = 'error'; // TODO to be configured
                    break;
                }
            }
            
            foreach ($routes as $route)
            {
                if (!empty($route['handler']))
                {
                    $handlerName = $route['handler'];
                    $action = '';
                }
                
                if (!empty($route['action']))
                {
                    $action = $route['action'];
                }
            }
            
        }
        
        echo "$handlerName/$action";
	    
	    
// 	    $handler = $this->container->create($handlerName);
// 	    if (isset($method))
// 	    {
// 	        $handler->$method($args);
// 	    }
	    
// 	    $handler->$method($args);
	}
}

?>