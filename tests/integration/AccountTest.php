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

class AccountTest extends HTTP_TestCase {

	public function getConnection() {
		$pdo = new PDO('mysql:host=127.0.0.1;dbname=hades_test', 'hades', 'hades');
		return $this->createDefaultDBConnection($pdo, 'hades_test');
	}

	public function getDataSet() {
		return $this->createFlatXMLDataSet(__DIR__.'/db_accounts.xml');
	}

	public function testInfo() {
		$d = $this->get('http://localhost:7676/info');
		$this->assertEquals($d->mode, 'test');
		$this->assertEquals(1, $d->account_count);
		$this->assertEquals('OK', $d->result);
	}

	public function testNewAccount() {
		$this->assertEquals(1, $this->getConnection()->getRowCount('accounts'));
		$d = $this->post('http://localhost:7676/v1/account/new',
			array(
				'mail' => 'test@qb9.net',
				'pass' => 'ranlogic2010',
				));
		$this->assertEquals('OK', $d->result);
		$this->assertEquals(2, $this->getConnection()->getRowCount('accounts'));
	}

}

?>
