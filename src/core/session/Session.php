<?php

namespace core\session;

interface Session
{
//     function start();
    
    function destroy();
    
    function isActive();
    
    function getStatusId();
    
    function getId();
    
    function regenerateId();
    
//     function setTimeout();
    
//     function isExpired();
}

?>