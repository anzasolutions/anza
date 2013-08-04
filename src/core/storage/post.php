<?php

namespace core\storage;

class Post extends Box
{
	public function __construct()
	{
	    parent::__construct($_POST);
	}
}

?>