<?php
function between($data, $start, $end)
{
    $data = stristr($data, $start); // Stripping all data from before $start
    $data = substr($data, strlen($start));  // Stripping $start
    $stop = stripos($data, $end);   // Getting the position of the $end of the data to scrape
    $data = substr($data, 0, $stop);    // Stripping all data from after and including the $end of the data to scrape
    return $data;   // Returning the scraped data from the function
}

$filename = "./index.html";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
fclose($handle);

$contents = explode('<blockquote>', $contents);

$table_content 	= array();
$clean_url 		= array();
$y = array();

$sarjana = array();
$magister = array();
$doktor = array();

foreach ($contents as $content) {
	if (strpos($content, '<table border=')) {
		array_push($table_content, $content);
	}
}	

foreach ($table_content as $content) {
	$cek = explode('<a ', $content);
	foreach ($cek as $c) {
		$x = between($c, 'href="', '</a>');
		if ($x != '') {
			array_push($clean_url, $x);
		}
	}
}

foreach ($clean_url as $clean) {
	$clean = str_replace(' target="_blank"', '', $clean);
	$clean = str_replace('<br />', '', $clean);

	$z = explode('">', $clean);
	if (strpos($z[1], 'Fakultas') !== false) {
		array_push($sarjana, $z);
	} else if (strpos($z[1], 'Doktor') !== false) {
		array_push($doktor, $z);
	} else {
		array_push($magister, $z);
	}
}

$response = array(
	'sarjana'  => $sarjana,
	'magister' => $magister,
	'doktor'   => $doktor
);
echo json_encode($response);
// echo print_r($magister);
// echo var_dump($table_content);
// echo $response;
 ?>