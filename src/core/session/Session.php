<?php

namespace core\session;

interface Session
{
    function start();
    
    function destroy();
    
    function getId();
    
//     function regenerateId();
    
}

?>