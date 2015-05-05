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
use QB9\Hades\Security;

class SecurityTest extends PHPUnit_Framework_TestCase {

	public function tearDown() {
		m::close();
	}

	public function testSecurityFail() {
		$request = m::mock('request');
		$request->shouldReceive('getBody')->times(1)->andReturn('test_body');
		$request->headers = m::mock('headers');
		$request->headers->shouldReceive('get')->times(1)->with('X-Security')->andReturn('hmac-sha1 301674fbadhashu3397ac37e4148dfe5c339d8ee');
		$response = m::mock('response');
		$response->shouldReceive('setStatus')->times(1)->with(401);

		$security = new Security('test_key');
		$result = $security->checkRequest($request, $response);

		$this->assertFalse($result);
	}

	public function testSecurityFailBadMethod() {
		$request = m::mock('request');
		$request->shouldReceive('getBody')->times(1)->andReturn('test_body');
		$request->headers = m::mock('headers');
		$request->headers->shouldReceive('get')->times(1)->with('X-Security')->andReturn('fail-this-test 301674fbadhashu3397ac37e4148dfe5c339d8ee');
		$response = m::mock('response');
		$response->shouldReceive('setStatus')->times(1)->with(401);

		$security = new Security('test_key');
		$result = $security->checkRequest($request, $response);

		$this->assertFalse($result);
	}

	public function testSecurityPass() {
		$request = m::mock('request');
		$request->shouldReceive('getBody')->times(1)->andReturn('test_body');
		$request->headers = m::mock('headers');
		$request->headers->shouldReceive('get')->times(1)->with('X-Security')->andReturn('hmac-sha1 301674f4a2f0a4d3397ac37e4148dfe5c339d8ee');
		$response = m::mock('response');

		$security = new Security('test_key');
		$result = $security->checkRequest($request, $response);

		$this->assertTrue($result);
	}

	public function testSecureClientRequest() {
		$request = m::mock('request');
		$request->shouldReceive('getBody')->times(1)->andReturn('test_body');
		$request->headers = m::mock('headers');
		$request->headers->shouldReceive('get')->times(1)->with('X-Security')->andReturn('hmac-sha1 301674f4a2f0a4d3397ac37e4148dfe5c339d8ee');
		$response = m::mock('response');
		$dao = m::mock('dao');
		$dao->shouldReceive('getClientKey')->times(1)->with('test_game')->andReturn('test_key');

		$result = Security::checkClientRequest($dao, 'test_game', $request, $response);

		$this->assertTrue($result);
	}

	public function testSecureServerRequest() {
		$request = m::mock('request');
		$request->shouldReceive('getBody')->times(1)->andReturn('test_body');
		$request->headers = m::mock('headers');
		$request->headers->shouldReceive('get')->times(1)->with('X-Security')->andReturn('hmac-sha1 301674f4a2f0a4d3397ac37e4148dfe5c339d8ee');
		$response = m::mock('response');
		$dao = m::mock('dao');
		$dao->shouldReceive('getServerKey')->times(1)->with('test_game')->andReturn('test_key');

		$result = Security::checkServerRequest($dao, 'test_game', $request, $response);

		$this->assertTrue($result);
	}

}

?>