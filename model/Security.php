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

class Security {

	var $key;

	static public function checkClientRequest($dao, $gameid, $request, $response) {
		$sec = new Security($dao->getClientKey($gameid));
		return $sec->checkRequest($request, $response);
	}

	static public function checkServerRequest($dao, $gameid, $request, $response) {
		$sec = new Security($dao->getServerKey($gameid));
		return $sec->checkRequest($request, $response);
	}

	public function __construct($key) {
		$this->key = $key;
	}

	public function checkRequest($request, $response) {
		$body = $request->getBody();
		$auth = $request->headers->get('X-Security');
		$parms = preg_split('/\s+/', $auth);
		if ( $parms[0] != 'hmac-sha1' ) {
			$response->setStatus(401);
			//error_log('bad auth method '.$auth);
			return false;
		}
		$hash = hash_hmac('sha1', $body, $this->key);
		if ( $hash != $parms[1] ) {
			$response->setStatus(401);
			//error_log('bad auth signature '.$auth);
			return false;
		}
		return true;
	}

}

?>