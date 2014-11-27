<?php 
session_start();

if (!isset($_SESSION['start'])) {
	$cookie 		= new Cookies();
	$cookie_path 	= $cookie->getCookiePath();

	// register path ke dalam session
	$_SESSION['cookie_path'] = $cookie_path;

	// set session start ke true agar tidak di eksekusi lagi
	$_SESSION['start'] 		 = true;

	// inisialisasi middleware
	$cookie_path = $_SESSION['cookie_path'];
	$middleware  = Middleware::checkInstance($cookie_path);
	$middleware->open('login.php', true);
}

$cookie_path = $_SESSION['cookie_path'];
$middleware  = Middleware::checkInstance($cookie_path);

//Define autoloader 
function __autoload($className) { 
	if (file_exists('classes/' . $className . '.php')) { 
	  require_once 'classes/' . $className . '.php'; 
	  return true; 
	} 
	return false; 
}

if (isset($_POST['ok'])):
	$response = $middleware->postLogin();
	echo $response;
else:	
	$captcha = $middleware->getCaptcha();
?>
<form action="" method="post">
	<input type="text" name="username">
	<input type="password" name="password">
	<input type="text" name="kode_captcha">
	<img src="data:image/jpeg;base64,<?php echo $captcha; ?>">
	<input type="submit" value="Login" name="ok">
</form>
<?php endif; ?>