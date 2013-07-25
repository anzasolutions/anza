<?php

use core\session\UserSession;

set_include_path(get_include_path() . PATH_SEPARATOR . 'src');
spl_autoload_register();

define('SESSION_END_REDIRECT_LOCATION', '/anza/error.php');
define('SESSION_DURATION_LIMIT', 1800);

$session = new UserSession();

?>

<a href="?session=destroy">destroy</a>
<a href="?session=start">start</a>
<a href="?session=regenerate">regenerate</a>

<?php

if (isset($_GET['session']) && $_GET['session'] == 'destroy')
{
    $session->destroyAndRedirect();
}

if (isset($_GET['add']) && $_GET['add'] == 'name')
{
    $session->name = 'andy';
}

if (isset($_GET['add']) && $_GET['add'] == 'country')
{
    $session->country = 'poland';
}

if (isset($_GET['session']) && $_GET['session'] == 'regenerate')
{
    $session->regenerateId();
}

if (isset($session->name))
    echo 'hi ' . $session->name;
if (isset($session->country))
    echo 'count ' . $session->country;
// echo $session->getId() . ' ' . $session->isActive();
echo $session->getId();

print_r($_SESSION);
// print_r($_SERVER);
print_r($_COOKIE);



// routing could work like this:
// 1. brake down controller, action and params
// 2. if controller is in request no session starts

?>