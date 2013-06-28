<?php
//use curl to send json data

$ch = curl_init();
$url = "http://www.test.com/records/api_submit";

$data = '{"first_name": "John", "last_name": "Smith", "cid": "58023890"}';

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$response = curl_exec($ch);
echo "<br>Curl Resonse:<br>";

$response = json_decode($response);
foreach ($response as $k=>$v) {
  echo $k .  ": " . $v. "<br>";
}

?>
