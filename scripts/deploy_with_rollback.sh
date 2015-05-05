#!/bin/bash

set -ex

cd "`dirname $0`/.."

DEST_SERVER_BK_FROM="${DEST_SERVER_BK_FROM:-/tmp/hades_test_install}"
DEST_SERVER_BK_TO="${DEST_SERVER_BK_TO:-/tmp/hades_deploy_bk}"
DEST_SERVER_HOST="${DEST_SERVER_HOST:-localhost}"
DEST_SERVER_USER="${DEST_SERVER_USER:-kthulhu}"
DB_HOST="${DB_HOST:-localhost}"
DB_NAME="${DB_NAME:-hades}"
DB_USER="${DB_USER:-hades}"
DB_PASS="${DB_PASS:-hades}"

ssh_run="ssh -o BatchMode=yes $DEST_SERVER_USER@$DEST_SERVER_HOST"
liquibase_run="liquibase/liquibase --url=jdbc:mysql://$DB_HOST/$DB_NAME --username=$DB_USER --password=$DB_PASS --changeLogFile=etc/db.changelog.xml"

$ssh_run "rm -rf $DEST_SERVER_BK_TO && mkdir -p $DEST_SERVER_BK_TO && rsync -cart --delete $DEST_SERVER_BK_FROM/ $DEST_SERVER_BK_TO"
$liquibase_run tag BEFORE_DEPLOY

if ! scripts/deploy.sh ; then
	echo
	echo "ROLLBACK!!"
	echo
	$liquibase_run rollback BEFORE_DEPLOY
	$ssh_run "rsync -cart --delete $DEST_SERVER_BK_TO/ $DEST_SERVER_BK_FROM"
	exit 1
fi

$ssh_run "rm -rf $DEST_SERVER_BK_TO"

