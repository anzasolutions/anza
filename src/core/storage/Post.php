<?php

namespace core\storage;

class Post
{
	public function __get($key)
	{
		if (isset($_POST[$key]))
		{
		    return $_POST[$key];
		}
	}
}

?>