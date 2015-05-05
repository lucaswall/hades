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

class RepositoryGames {

	var $db;

	public function __construct($db) {
		$this->db = $db;
	}

	public function getAndroidAPIKey($gameid) {
		$sth = $this->db->prepare('SELECT google_api_key FROM games WHERE gameid=:gameid');
		$sth->execute(array('gameid' => $gameid));
		if ( $sth->rowCount() < 1 ) return -1;
		return $sth->fetch()['google_api_key'];
	}

	public function getAppleAPNCert($gameid) {
		$sth = $this->db->prepare('SELECT apple_apn_cert_file FROM games WHERE gameid=:gameid');
		$sth->execute(array('gameid' => $gameid));
		if ( $sth->rowCount() < 1 ) return -1;
		return $sth->fetch()['apple_apn_cert_file'];
	}

	public function getClientKey($gameid) {
		$sth = $this->db->prepare('SELECT client_key FROM games WHERE gameid=:gameid');
		$sth->execute(array('gameid' => $gameid));
		if ( $sth->rowCount() < 1 ) return -1;
		return $sth->fetch()['client_key'];
	}

	public function getServerKey($gameid) {
		$sth = $this->db->prepare('SELECT server_key FROM games WHERE gameid=:gameid');
		$sth->execute(array('gameid' => $gameid));
		if ( $sth->rowCount() < 1 ) return -1;
		return $sth->fetch()['server_key'];
	}

	public function totalGamesCount() {
		$sth = $this->db->prepare('SELECT COUNT(*) AS cnt FROM games');
		$sth->execute();
		if ( $sth->rowCount() < 1 ) return -1;
		return $sth->fetch()['cnt'];
	}

}

?>
