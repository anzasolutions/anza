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
	}
	
	public function dissect()
	{
		$this->parts = explode('/', trim($_REQUEST['route'], '/'));
	}
	
	public function route()
	{
	    $handler = '';
	    $action = '';
	    $args = '';
	    $xpath = '//routes';
        
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
                    $handler = 'errorhandler'; // TODO to be configured
                    $action = ''; // TODO to be configured
                    break;
                }
                $args = $part;
            }
            
            foreach ($routes as $route)
            {
                if (!empty($route['handler']))
                {
                    $handler = (string) $route['handler'];
                    $action = '';
                    $args = '';
                }
                
                if (!empty($route['action']))
                {
                    $action = (string) $route['action'];
                }
            }
        }
        
        echo "$handler/$action ";
	    
	    $handler = $this->container->create($handler);
	    if (!empty($action))
	    {
	        $handler->$action($args);
	    }
	}
}

?>