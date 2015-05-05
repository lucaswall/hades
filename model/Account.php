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

class Account {

	var $dao;
	var $lastError = 'OK';

	public function getLastError() { return $this->lastError; }

	public function __construct($dao) {
		$this->dao = $dao;
	}

	public function newUser($mail, $pass) {
		$user = $this->dao->getUserByMail($mail);
		if ( $user != null ) {
			$this->lastError = 'USER_EXISTS';
			return false;
		}
		$uid = $this->dao->saveNewUser($mail, $this->cryptPassword($pass));
		return $this->dao->newSession($uid);
	}

	public function newSession($mail, $pass) {
		$user = $this->dao->getUserByMail($mail);
		if ( $user == null ) {
			$this->lastError = 'USER_NOT_FOUND';
			return false;
		}
		if ( ! $this->checkPassword($pass, $user['pass']) ) {
			$this->lastError = 'BAD_PASSWORD';
			return false;
		}
		return $this->dao->newSession($user['uid']);
	}

	public function totalCount() {
		return $this->dao->totalAccountsCount();
	}











	protected function cryptPassword($pass) {
		$salt = rand(100, 999);
		return $salt . ':' . md5($salt.$pass);
	}

	protected function checkPassword($passPlain, $passCrypt) {
		$salt = explode(':', $passCrypt)[0];
		$t = $salt . ':' . md5($salt.$passPlain);
		return $t == $passCrypt;
	}

}

?>