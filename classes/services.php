<?php
/**
* 
*/
class Services
{
	public function getCleanNameURL($html)
	{
		$contents = explode('<blockquote>', $html);

		$table_content 	= array();
		$clean_url 		= array();

		$sarjana 	= array();
		$magister 	= array();
		$doktor 	= array();

		foreach ($contents as $content) {
			if (strpos($content, '<table border=')) {
				array_push($table_content, $content);
			}
		}	

		foreach ($table_content as $content) {
			$explode = explode('<a ', $content);
			foreach ($explode as $exp) {
				$result	 = between($exp, 'href="', '</a>');

				if ($result != '') {
					array_push($clean_url, $result);
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

		return $response;
	}
}