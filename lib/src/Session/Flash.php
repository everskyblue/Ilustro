<?php

namespace Ilustro\Session;

use Ilustro\Session\Session;

class Flash
{
	
	public static function put($name, $msg)
	{
		if(!Session::exists($name)) {
			Session::set($name, $msg);
		}else{
			Session::delete($name);
		}
	}
	
	public static function get($name)
	{ 
		$get = Session::get($name);
		Session::delete($name);
		return $get;
	}
	
	public function exists($name)
	{
		return Session::exists($name);
	}
}