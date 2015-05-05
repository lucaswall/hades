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

class PushNotification {

	var $dao;
	var $lastError = 'OK';

	const OSTYPE_NULL = 0;
	const OSTYPE_ANDROID = 1;
	const OSTYPE_IOS = 2;

	var $pushDrivers = array();

	public function getLastError() { return $this->lastError; }

	public function __construct($dao) {
		$this->dao = $dao;
	}

	public function registerDriver($ostype, $driver) {
		$this->pushDrivers[$ostype] = $driver;
	}

	public function registerDevice($gameid, $userid, $devtoken, $ostype) {
		switch ( strtolower($ostype) ) {
			case 'android': $ostype_id = PushNotification::OSTYPE_ANDROID; break;
			case 'ios': $ostype_id = PushNotification::OSTYPE_IOS; break;
			default: $this->lastError = 'BAD_OSTYPE'; return false;
		}
		$this->dao->registerDevice($gameid, $userid, $devtoken, $ostype_id);
		return true;
	}

	public function unregisterDevice($devtoken) {
		$this->dao->unregisterDevice($devtoken);
	}

	public function sendNotification($gameid, $userid, $data) {
		$devices = $this->dao->getPushDevicesByUser($gameid, $userid);
		$sendByOs = array();
		foreach ( $devices as $dev ) {
			if ( ! isset($sendByOs[$dev['ostype']]) ) {
				$sendByOs[$dev['ostype']] = array();
			}
			$sendByOs[$dev['ostype']][] = $dev['token'];
		}
		$ret = true;
		foreach ( $sendByOs as $type => $tokens ) {
			$ret = $this->pushDrivers[$type]->sendNotification($tokens, $data);
		}
		return $ret;
	}

}

?>
