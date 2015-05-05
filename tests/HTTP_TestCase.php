<?php

/*
   Copyright 2015 QB9 S.A. Buenos Aires, Argentina

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/

require_once __DIR__.'/../vendor/autoload.php';

abstract class HTTP_TestCase extends PHPUnit_Extensions_Database_TestCase {

	public function get($url) {
		$ch = curl_init($url);
		return $this->execCurl($ch);
	}

	public function post($url, $vars, $key = null) {
		$data_string = json_encode($vars);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		$header = array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_string)
		);
		if ( $key ) $header[] = 'X-Security: hmac-sha1 ' . hash_hmac('sha1', $data_string, $key);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		return $this->execCurl($ch);
	}

	protected function execCurl($ch) {
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$out = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$this->assertEquals(0, curl_errno($ch), curl_error($ch));
		$this->assertEquals(200, $code, 'HTTP code '. $code . ' not 200!');
		curl_close($ch);
		$d = json_decode($out);
		$this->assertNotNull($d, 'Error decoding json response. '.$out);
		return $d;
	}

}

?>