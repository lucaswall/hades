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

class RepositoryPushNotification {

	var $db;

	public function __construct($db) {
		$this->db = $db;
	}

	public function registerDevice($gameid, $userid, $devtoken, $ostype) {
		$sth = $this->db->prepare('REPLACE INTO pushdevices (gameid, userid, devicetoken, ostype) VALUES (:gameid, :userid, :devicetoken, :ostype)');
		$sth->execute(array(':gameid' => $gameid, ':userid' => $userid, ':devicetoken' => $devtoken, ':ostype' => $ostype));
	}

	public function unregisterDevice($devtoken) {
		$sth = $this->db->prepare('DELETE FROM pushdevices WHERE devicetoken = :devicetoken');
		$sth->execute(array(':devicetoken' => $devtoken));
	}

	public function getPushDevicesByUser($gameid, $userid) {
		$sth = $this->db->prepare('SELECT devicetoken, ostype FROM pushdevices WHERE gameid=:gameid AND userid=:userid');
		$sth->execute(array(':gameid' => $gameid, ':userid' => $userid));
		$ret = array();
		while ( $d = $sth->fetch() ) {
			$ret[] = array('token' => $d['devicetoken'], 'ostype' => $d['ostype']);
		}
		return $ret;
	}

}

?>