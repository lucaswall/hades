#!/bin/bash

set -ex
cd "`dirname $0`/../liquibase"
./liquibase --url="jdbc:mysql://localhost/hades" --username=hades --password=hades --changeLogFile=../etc/db.changelog.xml update

