<?php

namespace core\authorization;

class Lopez
{
    private $session;
    private $permits;
    
	public function __construct()
	{
        $this->permits = simplexml_load_file(PERMITS_FILE);
	}
    
	public function sayHi($class, $method)
	{
	    $roles = array();
	    $roles = $this->lolo('//class[@name = "' . $class . '"]');
// 	    $roles = $this->lolo('//class[@name = "' . $class . '"]/method[@name = "' . $method . '"]');
	    
		if ($this->session->isStarted())
		{
		    $intersect = array_intersect($roles, $this->session->roles);
		    print_r($intersect);
		    return !empty($intersect);
		}
	}
    
	public function hasPermissions($class, $method)
	{
	    $roles = $this->lolo('//class[@name = "' . $class . '"]');
// 	    $roles = $this->lolo('//class[@name = "' . $class . '"]/method[@name = "' . $method . '"]');
		return !empty($roles);
	}
	
	private function lolo($xpath)
	{
	    $permits = $this->permits->xpath($xpath);
	    foreach ($permits as $permit)
	    {
	        $roles = (string) $permit['any'] != 'true' ? array_map('trim', explode(',', (string) $permit['roles'])) : array();
	        return $roles;
	    }
	}
}

?>