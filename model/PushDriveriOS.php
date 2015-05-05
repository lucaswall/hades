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

class PushDriveriOS implements IPushDriver {
	
	var $cert;
	var $apnServer;

	public function __construct($cert, $apnServer) {
		$this->cert = $cert;
		$this->apnServer = $apnServer;
	}

	public function sendNotification($tokens, $data) {

		$ctx = stream_context_create();
		$ret = stream_context_set_option($ctx, 'ssl', 'local_cert', $this->cert);
		if ( ! $ret ) return false;
		$sock = stream_socket_client($this->apnServer, $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $ctx);
		if ( ! $sock ) return false;

		$payload = json_encode($this->buildPayload($data));
		$id = 1;
		foreach ( $tokens as $token ) {
			$token_bin = base64_decode($token);
			$expiration = time() + 60*60*24*7;
			$priority = 5;
			$item1 = pack('Cn', 1, strlen($token_bin)) . $token_bin;
			$item2 = pack('Cn', 2, strlen($payload)) . $payload;
			$item3 = pack('CnN', 3, 4, $id++);
			$item4 = pack('CnN', 4, 4, $expiration);
			$item5 = pack('CnC', 5, 1, $priority);
			$frame = $item1 . $item2 . $item3 . $item4 . $item5;
			$command = pack('CN', 2, strlen($frame)) . $frame;
			$ret = fwrite($sock, $command);
		}
		fflush($sock);

		$sel_read = array($sock);
		$sel_write = null;
		$sel_except = null;
		if ( stream_select($sel_read, $sel_write, $sel_except, 0, 500000) ) {
			$d = fread($sock, 6);
			$err = unpack('Ccmd/Cstatus/Nidentifier', $d);
			error_log(var_export($err, true));
			return false;
		}

		fclose($sock);

		return true;
	}

	protected function buildPayload($data) {
		if ( isset($data['for_ios']) ) {
			return $data['for_ios'];
		}
		$ret = array();
		$ret['aps'] = array();
		if ( isset($data['title']) ) {
			$ret['aps']['alert'] = array();
			$ret['aps']['alert']['title'] = $data['title'];
			if ( isset($data['text']) ) $ret['aps']['alert']['body'] = $data['text'];
		} else if ( isset($data['text']) ) $ret['aps']['alert'] = $data['text'];
		if ( isset($data['badge']) ) $ret['aps']['badge'] = $data['badge'];
		if ( isset($data['sound']) ) $ret['aps']['sound'] = $data['sound'];
		if ( isset($data['extra']) ) {
			foreach ( $data['extra'] as $key => $value ) {
				$ret[$key] = $value;
			}
		}
		return $ret;
	}

}

?>