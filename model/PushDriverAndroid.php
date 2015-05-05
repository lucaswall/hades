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

namespace QB9\Hades;

require_once __DIR__.'/../vendor/autoload.php';

use QB9\Hades\IPushDriver;

class PushDriverAndroid implements IPushDriver {
	
	var $auth;

	public function __construct($auth) {
		$this->auth = $auth;
	}

	public function sendNotification($tokens, $data) {

		$data_string = json_encode(array(
			'registration_ids' => $tokens,
			'data' => $this->buildPayload($data),
			));
		$ch = curl_init('https://android.googleapis.com/gcm/send');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization: key=' . $this->auth,
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_string))
		);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$out = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		return true;
	}

	protected function buildPayload($data) {
		if ( isset($data['for_android']) ) {
			return $data['for_android'];
		}
		$ret = array();
		if ( isset($data['title']) ) $ret['title'] = $data['title'];
		if ( isset($data['text']) ) $ret['msg'] = $data['text'];
		if ( isset($data['badge']) ) $ret['badge'] = $data['badge'];
		if ( isset($data['sound']) ) $ret['sound'] = $data['sound'];
		if ( isset($data['extra']) ) {
			foreach ( $data['extra'] as $key => $value ) {
				$ret[$key] = $value;
			}
		}
		return $ret;
	}

}

?>