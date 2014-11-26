<?php 
//Define autoloader 
function __autoload($className) { 
	if (file_exists('classes/' . $className . '.php')) { 
	  require_once 'classes/' . $className . '.php'; 
	  return true; 
	} 
	return false; 
}

$middleware = new Middleware();
$middleware->open('login.php', true);