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

require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../HTTP_TestCase.php';

class PushNotificationTest extends HTTP_TestCase {

	public function getConnection() {
		$pdo = new PDO('mysql:host=127.0.0.1;dbname=hades_test', 'hades', 'hades');
		return $this->createDefaultDBConnection($pdo, 'hades_test');
	}

	public function getDataSet() {
		return $this->createFlatXMLDataSet(__DIR__.'/db_accounts.xml');
	}

	public function testRegisterDevice() {
		$this->assertEquals(1, $this->getConnection()->getRowCount('pushdevices'));
		$d = $this->post('http://localhost:7676/v1/push/register',
			array(
				'gameid' => 'test_game',
				'userid' => 'testuser',
				'device_token' => 'token127',
				'ostype' => 'android',
				),
			'client_key');
		$this->assertEquals('OK', $d->result);
		$this->assertEquals(2, $this->getConnection()->getRowCount('pushdevices'));
	}

	public function testUnregisterDevice() {
		$this->assertEquals(1, $this->getConnection()->getRowCount('pushdevices'));
		$d = $this->post('http://localhost:7676/v1/push/unregister',
			array(
				'gameid' => 'test_game',
				'device_token' => 'token99',
				),
			'client_key');
		$this->assertEquals('OK', $d->result);
		$this->assertEquals(0, $this->getConnection()->getRowCount('pushdevices'));
	}

	public function testSendNotification() {
		$d = $this->post('http://localhost:7676/v1/push/send',
			array(
				'gameid' => 'test_game',
				'userid' => 'kthulhu',
				'data' => array(
					'title' => 'Hey!',
					'msg' => 'Look at this!',
					'some_id' => 76,
					),
				),
			'server_key');
		$this->assertEquals('OK', $d->result);
		$this->assertEquals(array('token99'), $d->ret->tokens);
		$this->assertEquals(json_decode(json_encode(array(
			'title' => 'Hey!',
			'msg' => 'Look at this!',
			'some_id' => 76,
			))), $d->ret->data);
	}

}

?>
