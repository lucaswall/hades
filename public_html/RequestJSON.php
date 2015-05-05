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

class RequestJSON extends \Slim\Middleware {

	public function call() {
		$app = $this->app;
		if ( $app->request->isPost() && $app->request->getMediaType() != 'application/json' ) {
			$app->response->setStatus(400);
			$app->response->setBody('POST data must be application/json');
			return;
		}
		$env = $app->environment();
		$input = $env['slim.input'];
		$this->app->params = json_decode($input, true);
		if ( json_last_error() != JSON_ERROR_NONE ) {
			$app->response->setStatus(400);
			$app->response->setBody('Error parsing JSON POST data');
			return;
		}
		$this->next->call();
	}

}

?>