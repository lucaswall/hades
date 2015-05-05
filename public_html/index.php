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
use QB9\Hades\HadesFactory;
use QB9\Hades\Security;

require_once __DIR__.'/ResponseJSON.php';
require_once __DIR__.'/RequestJSON.php';

// production, test, development, staging
if ( ! isset($_ENV['SLIM_MODE']) ) {
	switch ( HadesBuild::BRANCH ) {
		default: $_ENV['SLIM_MODE'] = 'development'; break;
		case 'master': $_ENV['SLIM_MODE'] = 'production'; break;
		case 'develop': $_ENV['SLIM_MODE'] = 'staging'; break;
	}
}

$app = new \Slim\Slim();
$app->add(new ResponseJSON($json));
$app->add(new RequestJSON($json));

$cfg_file = __DIR__.'/../config/config_'.$app->mode.'.php';
if ( file_exists($cfg_file) ) require_once $cfg_file;

$db = new PDO('mysql:host='.$db_host.';dbname='.$db_name, $db_user, $db_pass);
$factory = new HadesFactory($db, $apple_apn_server);
$json = array('result' => 'UNKNOWN');

require_once __DIR__.'/../controller/info.php';
require_once __DIR__.'/../controller/account.php';
require_once __DIR__.'/../controller/push.php';

$app->run();

?>
