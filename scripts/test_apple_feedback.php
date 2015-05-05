<?php

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', '../etc/ios_apn_dev_cert.pem')
	or die('error setting certi');
$sock = stream_socket_client('tls://gateway.sandbox.push.apple.com:2195', $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $ctx);
var_dump($sock);
var_dump($errno);
var_dump($errstr);

echo "Connected to feedback service\n";

$sel_read = array($sock);
$sel_write = null;
$sel_except = null;
while ( stream_select($sel_read, $sel_write, $sel_except, 1, 0) ) {
	$d = fread($sock, 4+2+32);
	var_dump(unpack('Nstamp/nlen', $d));
	var_dump(base64_encode(substr($d, 4+2)));
}

echo "No more tuples\n";
fclose($sock);

?>
