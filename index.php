<?php

use core\session\UserSession;
use core\storage\Get;
use core\container\Container;
use core\container\ClasspathFinder;
use core\container\CreateException;
use core\storage\Post;

set_include_path(get_include_path() . PATH_SEPARATOR . 'src');
spl_autoload_register();

define('SESSION_END_REDIRECT_LOCATION', '/anza');
define('SESSION_DURATION_LIMIT', 60 * 60);
define('SESSION_DURATION_LIMIT_EXTENDED', 60 * 60 * 24 * 15);
define('SESSION_SALT', 'ABC');

define('DEBUG', true);

define('APPLICATION_ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/anza/src');

$get = new Get();
$session = new UserSession();

// try
// {

// class Controller
// {
//     private $accountService;

//     public function setAccountService(Get $service)
//     {
//         $this->accountService = $service;
//     }
    
//     public function setAccountService2(Get $service)
//     {
//     	$this->accountService = $service;
//     }

// }

// $c = new Controller();
// $ro = new \ReflectionClass($c);
// $m = $ro->getMethods();
// $p = $m[0]->getParameters();
// echo $p[0]->getClass()->name;
// $p2 = $m[1]->getParameters();
// echo $p2[0]->getClass()->name;
// print_r($p);
// $c->setAccountService();

//     Container::create('Post');
//     Container::create('core\storage\Box');

    $container = new Container();
    $container->singleton('get');
    $container->singleton('get');
    $container->singleton('get');
    
    
    echo Get::$count;

    $container->create('post');
    
    $container->bind('post2', function ()
    {
        return new Post();
    });
    
    $container->create('post2');
    $container->create('dupa');

//     $container = new Container();
//     $container->get('Get', true);
//     $container->get('Get', true);
//     $container->get('Get', true);
    
//     echo Get::$count;
    
//     $container->get('post');

//     $ggg = Container::get('Get', true);
//     $ggg = Container::get('Get', true);
//     $ggg = Container::get('Get', true);
    
//     echo Get::$count;
    
//     Container::get('post');
    
//     $ggg->lep = 'pep';
//     echo $ggg->lep;

//     Container::getInstance()->get;
//     $container = new Container();
//     $container->get;

//     $container = Container::getInstance();
//     $container->service->account;
//     $container->dao->user;
    
// }
// catch (CreateException $e)
// {
//     echo $e;
    // log
// }

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

?>