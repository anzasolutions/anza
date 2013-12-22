<?php

namespace core\authorization;

class Authorization
{
    private $session;
    private $permits;
    
    public function __construct()
    {
        $this->permits = simplexml_load_file(PERMITS_FILE);
    }
    
    public function isAuthorized()
    {
        $user = $this->session->user;
        
        // get user roles from session
        
        // check against auth.xml whether user has roles giving him access to given class and/or method
    }
    
    public function isRestricted()
    {
        ;
    }
}

?>