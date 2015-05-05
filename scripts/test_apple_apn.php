<?php

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', '../etc/ios_apn_dev_cert.pem')
	or die('error setting certi');
$sock = stream_socket_client('tls://gateway.sandbox.push.apple.com:2195', $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $ctx);
var_dump($sock);
var_dump($errno);
var_dump($errstr);


//$token = base64_decode('R01dm76TGuteN3xPDU23N23tmS7dr/6pV0Mj+Uwatiw=');
$token = base64_decode('pECbmyYZtVUdXDBdROhbXO2MJjE542CqBA4H3B69e6g=');
$payload = '{
	"aps" : {
		"alert": "Hello world!",
		"badge" : 10,
		"sound" : "default"
	},
	"some_id" : 76
}';

$item1 = pack('Cn', 1, strlen($token)) . $token;
$item2 = pack('Cn', 2, strlen($payload)) . $payload;
$item3 = pack('CnN', 3, 4, 2008); // notification identifier
$item4 = pack('CnN', 4, 4, time() + 60*60*24*7); // expiration date
$item5 = pack('CnC', 5, 1, 5); // priority
$frame = $item1 . $item2 . $item3 . $item4 . $item5;
$command = pack('CN', 2, strlen($frame)) . $frame;

$ret = fwrite($sock, $command);
echo 'send ret '; var_dump($ret);

$sel_read = array($sock);
$sel_write = null;
$sel_except = null;
if ( stream_select($sel_read, $sel_write, $sel_except, 5, 0) ) {
	echo "Got error message!\n";
	$d = fread($sock, 6);
	var_dump($d);
	var_dump(strlen($d));
	var_dump(unpack('Ccmd/Cstatus/Nidentifier', $d));
}

fclose($sock);

?>
