<?php

class Response 
{
	public static function notFound($message = NULL) 
	{
		header('HTTP/1.1 404 Not Found');
		header("status: 404 Not Found");
		if ($message) die($message);
		die('404 Not Found');
	}
}
