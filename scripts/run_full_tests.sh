#!/bin/bash

function finish {
	if [ "$server_pid" != "" ]; then
		kill $server_pid
	fi
}
trap finish EXIT

set -ex
cd "`dirname $0`/../liquibase"
./liquibase --url="jdbc:mysql://localhost/hades_test" --username=hades --password=hades --changeLogFile=../etc/db.changelog.xml update
cd ..
vendor/bin/phpunit tests/unit
php -S localhost:7676 -t public_html/ tests/router_test.php >server.log 2>&1 & server_pid=$!
sleep 2s
if ! vendor/bin/phpunit tests/integration ; then
	echo ========= SERVER.LOG =========
	cat server.log
	exit 1
fi
