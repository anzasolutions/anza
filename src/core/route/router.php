<?php

namespace core\route;

/**
 * Invokes class and methods for any url
 * as defined in the route mapping file. 
 * @author anza
 */
class Router
{
    private $container;
    private $routes;
    private $parts;
    
	public function __construct()
	{
	    $this->routes = simplexml_load_file(ROUTES_FILE);
		$this->parts = explode('/', trim($_REQUEST['route'], '/'));
	}
	
	public function route()
	{
	    $handler = '';
	    $action = '';
	    $args = '';
	    $xpath = '//routes';
        
	    // let's search for route definitions of url path elements
        foreach ($this->parts as $part)
        {
            $xpath .= '/route[@path = "' . $part . '"]';
            $routes = $this->routes->xpath($xpath);
            
            if (empty($routes))
            {
                // when path is not found maybe because it's an argument? 
                $xpath = substr_replace($xpath, '{args}', strrpos($xpath, $part), strlen($part));
                $routes = $this->routes->xpath($xpath);
                if (empty($routes))
                {
                    // when we know the path is not defined in any way let's go to an error handler
                    $handler = NOT_FOUND_HANDLER;
                    $action = NOT_FOUND_ACTION;
                    break;
                }
                // ok, the path is an argument, so let's keep its value for later use
                $args = $part;
            }
            
            foreach ($routes as $route)
            {
                if (!empty($route['handler']))
                {
                    // when switching handler we must reset action and arguments
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