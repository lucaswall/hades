#!/bin/bash

set -ex

cd "`dirname $0`"

export DEST_SERVER_BK_FROM="/var/www/hades_prod"
export DEST_SERVER_BK_TO="/tmp/hades_prod_backup"
export DEST_SERVER_HOST=""
export DEST_SERVER_USER="www-data"
export DEST_SERVER_TEMP="/tmp/hades_prod_deploy"
export LIBS_DIR="/var/www/hades_prod"
export HTDOCS_DIR="/var/www/hades_prod/htdocs"
export DB_HOST=""
export DB_NAME=""
export DB_USER=""
export DB_PASS=""
export DEST_SERVER_HTTP="http://mobileapi.qb9.net"

./deploy_with_rollback.sh

