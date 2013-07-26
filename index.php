<?php

use core\session\UserSession;

set_include_path(get_include_path() . PATH_SEPARATOR . 'src');
spl_autoload_register();

define('SESSION_END_REDIRECT_LOCATION', '/anza');
define('SESSION_DURATION_LIMIT', 1800);
define('SESSION_SALT', 'ABC');
define('DEBUG', true);

$session = new UserSession();

if (isset($_GET['login']) && $_GET['login'] == 'andy')
{
    $session->start();
    header('Location: ' . SESSION_END_REDIRECT_LOCATION);
}

if (!$session->isStarted())
{

?>

<form action="/anza" method="get">
    <input type="text" name="login" />
    <input type="submit" value="send" />
</form>

<?php

}
else
{

?>

<a href="?session=destroy">destroy</a>

<?php

}

if (isset($_GET['session']) && $_GET['session'] == 'destroy')
{
    $session->destroyAndRedirect();
}

if (DEBUG)
{
    echo $session->getId();
    
    print_r($_SESSION);
    // print_r($_SERVER);
    print_r($_COOKIE);
}

?>