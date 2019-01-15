<?php

namespace Kowo\Ilustro\Session;

class Security
{
    /**
     * 
     */
	protected static $length;
	
	/**
	 * 
	 */
	protected static $hash = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	
	/**
	 * 
	 */
	public static function crsf()
	{
		self::$length = strlen(self::$hash);
		$rand = '';
		for($x = 0; $x < self::$length; $x++){
			$rand .= self::$hash[rand(0, self::$length - 1)];
		}
		$unique = md5(uniqid($rand));
		if(!Session::exists('crsf-token')){
			 Session::set('crsf-token', $unique);
		}
		return Session::get("crsf-token");
	}
	
	/**
	 * 
	 * 
	 */
	public static function isCrsf($token = 'x-crsf-token')
	{
		$is = (Session::exists('crsf-token') && Session::get('crsf-token') === Input::item($token)) ? true : false;
		Session::delete("crsf-token");
		return $is;
	}
}