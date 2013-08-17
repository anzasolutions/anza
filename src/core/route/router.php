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
// 	    print_r($this->routes);
	    $this->dissect();
	    $this->route();
	}
	
	public function dissect()
	{
		$this->parts = explode('/', trim($_REQUEST['route'], '/'));
// 		print_r($this->parts);
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
            
            if (empty($routes))
            {
//                 header('Location: ' . NOT_FOUND_PAGE);
                $handlerName = 'errorcontroller';
                $action = 'error';
                break;
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