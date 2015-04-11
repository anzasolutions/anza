<?php

namespace core\config;

class Config
{
    public function __construct() {
        
        echo 'koko';
        var_dump($_SERVER);
        
//         define('INSTALLATION_ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/anza');
        
//         // Load
//         $config = new SimpleXMLElement( file_get_contents( 'config.xml' ) );
        
//         // Grab parts of option1
//         foreach( $config->option1->attributes() as $var )
//         {
//             echo $var.' ';
//         }
        
//         // Grab option2
//         echo 'likes '.$config->option2['fav_dessert'];
    }
}

?>