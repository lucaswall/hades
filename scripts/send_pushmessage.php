<?php

$data_string = json_encode(array(
	'gameid' => 'net.qb9.notifsample',
	'userid' => 'kthulhu',
	'data' => array(
		'title' => 'Hello!',
		'text' => 'Look this now!',
		'badge' => 76,
		'sound' => 'default',
		'extra' => array(
			'some_id' => 76,
		),
	),
), JSON_PRETTY_PRINT);

#$ch = curl_init('http://localhost/~kthulhu/hades/index.php/push/send');
$ch = curl_init('http://mobileapi-test.qb9.net/v1/push/send');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json',
	'Content-Length: ' . strlen($data_string),
	'X-Security: hmac-sha1 ' . hash_hmac('sha1', $data_string, '5e4f00e9e73a2657dc4a5ea36d91786d587c326e'),
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$out = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$header_out = curl_getinfo($ch, CURLINFO_HEADER_OUT);
curl_close($ch);

var_dump($header_out);
var_dump($data_string);
var_dump($code);
var_dump($out);

?>
