<?php
//get REQUEST_METHOD, GET or POST
$method = $_SERVER['REQUEST_METHOD'];

$url = "http://www.mytest.com/records/api_submit";
$postdata = '{"first_name": "John", "last_name": "Smith", "cid": "890023788"}';
if($method == 'GET') {
  $context = null;
} elseif ($method == 'POST') {
	$opts = array('http' =>
				array(
						'method' => 'POST',
						'header' => 'Content-type: application/json',
						'content' => $postdata
				)
	);
	$context = stream_context_create($opts);
}

$response = file_get_contents($url, false, $context);


$response = json_decode($response);
foreach ($response as $k=>$v) {
  echo $k .  ": " . $v. "<br>";
}

?>
