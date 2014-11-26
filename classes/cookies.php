<?php 
/**
* Cookies Handler
*/
class Cookies
{
	public static $_cookie_path;

	function __construct() {
		$path = './cookies/cookie-' . rand() . time() . '.txt';
		if (file_exists($path)) {
			$this->__construct();
		} else {
			$fp   = fopen($path, 'w');
			fclose($fp);

			$this->setCookiePath($path);
		}
	}

	public function setCookiePath($path)
	{
		self::$_cookie_path = $path;
	}

	public function getCookiePath()
	{
		return self::$_cookie_path;
	}
}