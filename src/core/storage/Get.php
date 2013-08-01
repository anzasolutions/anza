<?php

namespace core\storage;

class Get
{
	public function __get($key)
	{
		if (isset($_GET[$key]))
		{
		    return $_GET[$key];
		}
	}
}

?>