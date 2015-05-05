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

use \Mockery as m;
use QB9\Hades\PushNotification;

class PushNotificationTest extends PHPUnit_Framework_TestCase {

	public function tearDown() {
		m::close();
	}

	public function testRegisterDevice() {
		$dao = m::mock('dao');
		$dao->shouldReceive('registerDevice')->times(1)->with('test_game', 'kthulhu', 'token99', 1)->andReturn(true);

		$push = new PushNotification($dao);
		$ret = $push->registerDevice('test_game', 'kthulhu', 'token99', 'android');

		$this->assertEquals(true, $ret);
	}

	public function testUnregisterDevice() {
		$dao = m::mock('dao');
		$dao->shouldReceive('unregisterDevice')->times(1)->with('token98');

		$push = new PushNotification($dao);
		$ret = $push->unregisterDevice('token98');

	}

	public function testSendNotification() {
		$dao = m::mock('dao');
		$devices = array(
				array('token' => 'token1', 'ostype' => 1),
				array('token' => 'token2', 'ostype' => 1),
				array('token' => 'token3', 'ostype' => 2),
			);
		$dao->shouldReceive('getPushDevicesByUser')->times(1)->with('net.qb9.notifsample', 'test_user')->andReturn($devices);

		$notifData = array(
			'title' => 'Hey!',
			'msg' => 'Look at this!',
			'some_id' => 76,
			);
		$driver1 = m::mock('driver1');
		$driver1->shouldReceive('sendNotification')->times(1)->with(array('token1', 'token2'), $notifData);
		$driver2 = m::mock('driver1');
		$driver2->shouldReceive('sendNotification')->times(1)->with(array('token3'), $notifData);

		$push = new PushNotification($dao);
		$push->registerDriver(1, $driver1);
		$push->registerDriver(2, $driver2);
		$ret = $push->sendNotification('net.qb9.notifsample', 'test_user', $notifData);

	}

}

?>