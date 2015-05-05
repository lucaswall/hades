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

use QB9\Hades\RepositoryAccount;
use QB9\Hades\RepositoryPushNotification;
use QB9\Hades\RepositoryGames;
use QB9\Hades\Account;
use QB9\Hades\PushNotification;
use QB9\Hades\PushDriverNull;
use QB9\Hades\PushDriverAndroid;
use QB9\Hades\PushDriveriOS;

class HadesFactory {

	protected $db;
	protected $appleAPNServer;

	public function __construct($db, $appleAPNServer) {
		$this->db = $db;
		$this->appleAPNServer = $appleAPNServer;
	}

	public function getAccount() {
		return new Account(new RepositoryAccount($this->db));
	}

	public function getPushNotification($gameid) {
		$push = new PushNotification(new RepositoryPushNotification($this->db));
		$regGames = $this->getRepositoryGames();
		$push->registerDriver(PushNotification::OSTYPE_NULL, new PushDriverNull());
		$push->registerDriver(PushNotification::OSTYPE_ANDROID, new PushDriverAndroid($regGames->getAndroidAPIKey($gameid)));
		$push->registerDriver(PushNotification::OSTYPE_IOS, new PushDriveriOS($regGames->getAppleAPNCert($gameid), $this->appleAPNServer));
		return $push;
	}

	public function getRepositoryGames() {
		return new RepositoryGames($this->db);
	}

}

?>