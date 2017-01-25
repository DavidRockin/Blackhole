<?php

namespace App;

class Auth
{
	
	public static function isLoggedIn()
	{
		return isset($_SESSION['LOGGED_IN']) && $_SESSION['LOGGED_IN'] === true;
	}
	
	public static function getUserId()
	{
		return self::isLoggedIn() ? intval($_SESSION['USER_ID']) : null;
	}
	
}
