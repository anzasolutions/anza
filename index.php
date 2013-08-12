<?php

use core\session\UserSession;
use core\storage\Get;
use core\container\Container;
use core\container\ClasspathFinder;
use core\container\CreateException;
use core\storage\Post;
use core\storage\Controller;
use core\storage\Server;

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

$container = new Container();
$get = $container->singleton('get');
$session = $container->singleton('usersession');

$container->create('post');

$container->bind('post2', function ()
{
    return new Post();
});

$container->create('post2');
$container->create('get');

$controller = $container->create('controller');

print_r($controller);

echo Get::$count;

//     $container = Container::getInstance();
//     $container->service->account;
//     $container->dao->user;
    
// Box::get($session, 'name');

// Box::get()->session->name;
// Box::get($session)->name;

if ($get->login == 'andy')
{
    if ($get->remember)
    {
        $session->setExtended();
    }
    $session->start();
    header('Location: ' . SESSION_END_REDIRECT_LOCATION);
}

if (!$session->isStarted())
{

?>

<form action="/anza" method="get">
	<input type="text" name="login" /> <input type="checkbox"
		name="remember" /> <input type="submit" value="send" />
</form>

<?php

}
else
{

?>

<a href="?session=off">destroy</a>

<?php

}

if ($get->session == 'off')
{
    $session->destroyAndRedirect();
}

if (DEBUG)
{
    echo $session->getId();
    
    print_r($_SESSION);
//     print_r($_SERVER);
    print_r($_COOKIE);
}

echo $container->singleton('server')->HTTP_USER_AGENT;

?>