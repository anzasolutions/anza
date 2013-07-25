<?php

use core\session\UserSession;

set_include_path(get_include_path() . PATH_SEPARATOR . 'src');
spl_autoload_register();

echo 'session expired...';

$session = new UserSession();

?>