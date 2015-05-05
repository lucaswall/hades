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

class RepositoryAccount {

	var $db;

	public function __construct($db) {
		$this->db = $db;
	}

	public function getUserByMail($mail) {
		$sth = $this->db->prepare('SELECT * FROM accounts WHERE mail = :mail');
		$sth->execute(array(':mail' => $mail));
		if ( $sth->rowCount() < 1 ) return null;
		return $this->parseUser($sth->fetch());
	}

	public function saveNewUser($mail, $pass) {
		$sth = $this->db->prepare('INSERT INTO accounts (mail, pass) VALUES (:mail, :pass)');
		$sth->execute(array(':mail' => $mail, ':pass' => $pass));
		return $this->db->lastInsertId();
	}

	public function totalAccountsCount() {
		$sth = $this->db->prepare('SELECT COUNT(*) AS cnt FROM accounts');
		$sth->execute();
		if ( $sth->rowCount() < 1 ) return -1;
		return $sth->fetch()['cnt'];
	}

	public function newSession($uid) {
		$sth = $this->db->prepare('INSERT INTO sessions (uid, sessionid) VALUES (:uid, :sessionid)');
		$sessionid = md5($uid . microtime());
		$sth->execute(array(':uid' => $uid, ':sessionid' => $sessionid));
		return $sessionid;
	}





	protected function parseUser($d) {
		return array(
			'uid' => $d['uid'],
			'mail' => $d['mail'],
			'pass' => $d['pass'],
		);
	}

}

?>