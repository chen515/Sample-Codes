<?php
$url="http://en.wikipedia.org/w/api.php?action=opensearch&search=uk&format=xml&limit=5";
$timeout = 0; // set to zero for no timeout

$ch = curl_init();

curl_setopt_array($ch, array(
  CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_CONNECTTIMEOUT => $timeout
));

$xml_data = curl_exec($ch);


if($xml_data) {
	echo "Search Results<br>========================== <br>";
	$parser = simplexml_load_string($xml_data);
	foreach($parser->Section as $section) {
		foreach($section->Item as $item) {
			echo "{$item->Text}<br>{$item->Description}<br>--------------------<br>";
		}
	}
}

curl_close($ch);
?>
