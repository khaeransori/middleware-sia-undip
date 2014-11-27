<?php
/**
* Middleware Class
*/
class Middleware
{
	private $_cookie_path;
	private static $_instance;

	const BASE_URL 		= 'https://sift.undip.ac.id/';
	const LOGIN_URL 	= 'login.php';
	const CAPTCHA_URL 	= 'securimage/securimage_show.php';

	function __construct($path) {
		$this->_cookie_path = $path;
	}

	public static function checkInstance($path)
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new Middleware($path);
		}

		return self::$_instance;
	}

	public function open($url, $init = false, $method = null, $data = null, $binary = false)
	{
		$url = self::BASE_URL . $url;

		// Assigning cURL options to an array		
		$options = array(
			CURLOPT_HEADER          => FALSE,
			CURLOPT_RETURNTRANSFER 	=> TRUE,  // Setting cURL's option to return the webpage data
			CURLOPT_SSL_VERIFYPEER 	=> FALSE, // Setting cURL's option to do not verify certificate
			CURLOPT_FOLLOWLOCATION 	=> TRUE,  // Setting cURL to follow 'location' HTTP headers
			CURLOPT_AUTOREFERER 	=> TRUE, // Automatically set the referer where following 'location' HTTP headers
			CURLOPT_CONNECTTIMEOUT 	=> 120,   // Setting the amount of time (in seconds) before the request times out
			CURLOPT_TIMEOUT 		=> 120,  // Setting the maximum amount of time for cURL to execute queries
			CURLOPT_MAXREDIRS 		=> 10, // Setting the maximum number of redirections to follow
			CURLOPT_USERAGENT 		=> "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1a2pre) Gecko/2008073000 Shredder/3.0a2pre ThunderBrowse/3.2.1.8",  // Setting the useragent
			CURLOPT_URL 			=> $url, // Setting cURL's URL option with the $url variable passed into the function
		);
		
		if ($init) {
			$options[CURLOPT_COOKIEJAR] 	 = realpath(dirname(__FILE__) . '/..') . $this->_cookie_path;
		} else {
			$options[CURLOPT_COOKIEFILE] 	 = realpath(dirname(__FILE__) . '/..') . $this->_cookie_path;
		}

		if ($method == 'post') {
			$postlength = strlen($data);

			$options[CURLOPT_POST]		 	 = $postlength;
			$options[CURLOPT_POSTFIELDS] 	 = $data;
		}

		if ($binary) {
			$options[CURLOPT_BINARYTRANSFER] = TRUE;
		}

		// Initialize session and set URL.
		$ch = curl_init();

		// Setting cURL's options using the previously assigned array data in $options    
		curl_setopt_array($ch, $options);

		// Get the response and close the channel.
		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

	public function between($data, $start, $end)
	{
        $data = stristr($data, $start); // Stripping all data from before $start
        $data = substr($data, strlen($start));  // Stripping $start
        $stop = stripos($data, $end);   // Getting the position of the $end of the data to scrape
        $data = substr($data, 0, $stop);    // Stripping all data from after and including the $end of the data to scrape
        return $data;   // Returning the scraped data from the function
    }

    public function getCaptcha()
    {
    	$url  	  = self::CAPTCHA_URL . '?' . rand();
    	$response = $this->open($url, false, null, null, true);

    	return base64_encode($response);
    }

    public function postLogin()
    {
    	// mengambil semua data yang telah di post
    	$username = $_POST['username'];
    	$password = $_POST['password'];
    	$captcha  = $_POST['kode_captcha'];

    	// jadikan satu
    	$data = "username=" . $username . "&password=" . $password . "&kode_captcha=" . $captcha . "&ok=Login";

    	// jadikan 1 jalur koneksi
	    $response  = $this->open(self::LOGIN_URL, false, 'post', $data);

	    $error 	   = false;
	    $data      = '';

	    // cek apakah ada error atau tidak
	    if (strpos($response, 'error')) {
	    	$error = true;
	    } else {
	    	$service = new Services();
	    	$data    = $service->getCleanNameURL($response);
	    }

	    $response = array(
	    	'error' => $error, 
	    	'data'  => $data
	    );

	    return json_encode($response);
    }
}