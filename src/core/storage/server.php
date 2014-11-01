<?php

namespace core\storage;

class Server extends Box
{

    public function __construct()
    {
        parent::__construct($_SERVER);
    }
}

?>