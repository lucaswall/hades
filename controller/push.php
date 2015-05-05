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

use QB9\Hades\HadesBuild;
use QB9\Hades\Security;

$app->post('/v1/push/register', function() use ($app, $factory, &$json) {
	$gameid = $app->params['gameid'];
	$userid = $app->params['userid'];
	$device_token = $app->params['device_token'];
	$ostype = $app->params['ostype'];

	if ( ! Security::checkClientRequest($factory->getRepositoryGames(), $gameid, $app->request, $app->response) ) {
		return;
	}

	$push = $factory->getPushNotification($gameid);
	$push->registerDevice($gameid, $userid, $device_token, $ostype);
	$json['result'] = $push->getLastError();
});

$app->post('/v1/push/unregister', function() use ($app, $factory, &$json) {
	$gameid = $app->params['gameid'];
	$device_token = $app->params['device_token'];

	if ( ! Security::checkClientRequest($factory->getRepositoryGames(), $gameid, $app->request, $app->response) ) {
		return;
	}

	$push = $factory->getPushNotification($gameid);
	$push->unregisterDevice($device_token);
	$json['result'] = $push->getLastError();
});

$app->post('/v1/push/send', function() use ($app, $factory, &$json) {
	$gameid = $app->params['gameid'];
	$userid = $app->params['userid'];
	$data = $app->params['data'];

	if ( ! Security::checkServerRequest($factory->getRepositoryGames(), $gameid, $app->request, $app->response) ) {
		return;
	}

	$push = $factory->getPushNotification($gameid);
	$ret = $push->sendNotification($gameid, $userid, $data);
	$json['result'] = $push->getLastError();
	$json['ret'] = $ret;
});

?>