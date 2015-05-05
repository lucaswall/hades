#!/bin/bash

set -ex

cd "`dirname $0`"

export DEST_SERVER_BK_FROM="/var/www/hades_stage"
export DEST_SERVER_BK_TO="/tmp/hades_stage_backup"
export DEST_SERVER_HOST=""
export DEST_SERVER_USER="www-data"
export DEST_SERVER_TEMP="/tmp/hades_stage_deploy"
export LIBS_DIR="/var/www/hades_stage"
export HTDOCS_DIR="/var/www/hades_stage/htdocs"
export DB_HOST=""
export DB_NAME=""
export DB_USER=""
export DB_PASS=""
export DEST_SERVER_HTTP="http://mobileapi-test.qb9.net"

./deploy_with_rollback.sh

