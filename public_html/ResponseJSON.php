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

require_once __DIR__.'/../vendor/autoload.php';

class ResponseJSON extends \Slim\Middleware {

	public $d = array();

	public function __construct(&$ref) {
		$this->d = &$ref;
	}

	public function call() {
		$app = $this->app;
		$this->next->call();
		$response = $app->response;
		if ( $response->isOk() ) {
			$response->headers->set('Content-Type', 'application/json');
			$response->setBody(json_encode($this->d));
		}
	}

}

?>