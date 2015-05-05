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
use QB9\Hades\Account;

$test_user = array(
	'uid' => 1,
	'mail' => 'wall.lucas@gmail.com',
	'pass' => '666:36f114345cce6a03bf00938c90023964',
);

class AccountTest extends PHPUnit_Framework_TestCase {

	public function tearDown() {
		m::close();
	}

	public function testNewAccount() {
		global $test_user;
		$dao = m::mock('dao');
		$dao->shouldReceive('getUserByMail')->times(1)->with('wall.lucas@gmail.com')->andReturn(null);
		$dao->shouldReceive('saveNewUser')->times(1)->with('wall.lucas@gmail.com', m::any())->andReturn(1);
		$dao->shouldReceive('newSession')->times(1)->with(1)->andReturn(8);

		$account = new Account($dao);
		$ret = $account->newUser('wall.lucas@gmail.com', 'ranlogic2008');

		$this->assertEquals(8, $ret);
	}

	public function testNewAccountExists() {
		global $test_user;
		$dao = m::mock('dao');
		$dao->shouldReceive('getUserByMail')->times(1)->with('wall.lucas@gmail.com')->andReturn($test_user);

		$account = new Account($dao);
		$ret = $account->newUser('wall.lucas@gmail.com', 'ranlogic2008');
		
		$this->assertFalse($ret);
		$this->assertEquals('USER_EXISTS', $account->getLastError());
	}

	public function testNewSession() {
		global $test_user;
		$dao = m::mock('dao');
		$dao->shouldReceive('getUserByMail')->times(1)->with('wall.lucas@gmail.com')->andReturn($test_user);
		$dao->shouldReceive('newSession')->times(1)->with(1)->andReturn(5);

		$account = new Account($dao);
		$sessId = $account->newSession('wall.lucas@gmail.com', 'ranlogic2008');

		$this->assertEquals(5, $sessId);
	}

	public function testNewUserNotFound() {
		$dao = m::mock('dao');
		$dao->shouldReceive('getUserByMail')->times(1)->with('wall.lucas@gmail.com')->andReturn(null);

		$account = new Account($dao);
		$sessId = $account->newSession('wall.lucas@gmail.com', 'ranlogic2008');

		$this->assertFalse($sessId);
		$this->assertEquals('USER_NOT_FOUND', $account->getLastError());
	}

	public function testNewSessionBadPassword() {
		global $test_user;
		$dao = m::mock('dao');
		$dao->shouldReceive('getUserByMail')->times(1)->with('wall.lucas@gmail.com')->andReturn($test_user);

		$account = new Account($dao);
		$sessId = $account->newSession('wall.lucas@gmail.com', 'ranlogic2009');

		$this->assertFalse($sessId);
		$this->assertEquals('BAD_PASSWORD', $account->getLastError());
	}

	public function testAccountCount() {
		$dao = m::mock('dao');
		$dao->shouldReceive('totalAccountsCount')->times(1)->andReturn(7);

		$account = new Account($dao);
		$count = $account->totalCount();

		$this->assertEquals(7, $count);
	}

}

?>
