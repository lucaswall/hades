<?php

if ( ! isset($argv[1]) ) {
	echo "usage: smoke_test.php <base_url>\n";
	exit(1);
}

$url = $argv[1].'/info';
echo "Requesting info from $url\n";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$out = curl_exec($ch);
if ( ! $out ) {
	echo "ERROR! ==> Error fetching data!\n";
	echo curl_error($ch)."\n";
	exit(1);
}
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "Got code $code\n";
if ( $code != 200 ) {
	echo "ERROR! ==> Bad code!\n";
	echo $out."\n";
	exit(1);
}
curl_close($ch);

$d = json_decode($out);
if ( ! $d ) {
	echo "ERROR! ==> Error parsing JSON!\n";
	echo $out."\n";
	exit(1);
}

echo "Accounts count = " . $d->account_count . "\n";
echo "Games count = " . $d->games_count . "\n";
echo "Version = " . $d->version . "\n";
echo "Build Number = " . $d->build_number . "\n";
echo "Build ID = " . $d->build_id . "\n";
echo "Branch = " . $d->branch . "\n";

if ( $d->account_count < 0 || $d->games_count < 1 ) {
	echo "ERROR! ==> Strange data read from server!\n";
	exit(1);
}

echo "OK!\n\n";

?>
