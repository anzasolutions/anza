<?php

namespace core\storage;

class Get extends Box
{
	public function __construct()
	{
	    parent::__construct($_GET);
	}
}

?>