<?php

use core\container\Container;
use core\storage\Post;

set_include_path(get_include_path() . PATH_SEPARATOR . 'src');
spl_autoload_register();

define('INSTALLATION_ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/anza');
define('APPLICATION_ROOT_PATH', INSTALLATION_ROOT_PATH . '/src');

define('SESSION_END_REDIRECT_LOCATION', '/anza');
define('SESSION_DURATION_LIMIT', 60 * 60);
define('SESSION_DURATION_LIMIT_EXTENDED', 60 * 60 * 24 * 15);
define('SESSION_SALT', 'ABC');

define('DEBUG', true);

define('INJECT_FILE', INSTALLATION_ROOT_PATH . '/resources/config/inject.xml');
define('ROUTES_FILE', INSTALLATION_ROOT_PATH . '/resources/config/route.xml');

define('NOT_FOUND_HANDLER', 'errorhandler');
define('NOT_FOUND_ACTION', '');

$container = new Container();
$router = $container->create('router');
$router->route();

$get = $container->single('get');
$session = $container->single('usersession');

if (!$session->isStarted())
{

?>

<form action="/anza/login" method="post">
	<input type="text" name="login" />
	<input type="checkbox" name="remember" />
	<input type="submit" value="send" />
</form>

<?php

}
else
{

?>

<a href="/anza/login/destroy">destroy</a>

<?php

}

if (DEBUG)
{
    echo $session->getId();
    
    print_r($_SESSION);
//     print_r($_SERVER);
    print_r($_COOKIE);
}

echo $container->single('server')->HTTP_USER_AGENT;

?>