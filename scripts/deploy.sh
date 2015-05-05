#!/bin/bash

did_ssh=
function finish {
	if [ -d "$tmp_dir" ]; then
		rm -rf $tmp_dir
	fi
	if [ "z$did_ssh" != "z" ]; then
		$ssh_run "rm -rf \"$DEST_SERVER_TEMP\""
	fi
}
trap finish EXIT

set -ex

DEST_SERVER_HOST="${DEST_SERVER_HOST:-localhost}"
DEST_SERVER_USER="${DEST_SERVER_USER:-kthulhu}"
DEST_SERVER_TEMP="${DEST_SERVER_TEMP:-/tmp/hades_deploy}"
LIBS_DIR="${LIBS_DIR:-/tmp/hades_test_install}"
HTDOCS_DIR="${HTDOCS_DIR:-/tmp/hades_test_install/htdocs}"
DB_HOST="${DB_HOST:-localhost}"
DB_NAME="${DB_NAME:-hades}"
DB_USER="${DB_USER:-hades}"
DB_PASS="${DB_PASS:-hades}"
DEST_SERVER_HTTP="${DEST_SERVER_HTTP:-http://localhost/~kthulhu/hades/index.php}"

cd "`dirname $0`/.."

tmp_dir="/tmp/hades_deploy_$$"

rm -rf "$tmp_dir"
mkdir -pv "$tmp_dir/libs"
rsync -cart --delete model/ "$tmp_dir/libs/model"
rsync -cart --delete config/ "$tmp_dir/libs/config"
rsync -cart --delete controller/ "$tmp_dir/libs/controller"
rsync -cart --delete vendor/ "$tmp_dir/libs/vendor"
mkdir -pv "$tmp_dir/htdocs"
rsync -cart --delete public_html/ "$tmp_dir/htdocs"
mv -fv "$tmp_dir/htdocs/index.php" "$tmp_dir/htdocs/index.php.orig";
cp -fv etc/index.php.down "$tmp_dir/htdocs/index.php";
cp -fv scripts/install.sh "$tmp_dir/"

echo "LIBS_DIR=\"$LIBS_DIR\"" >> "$tmp_dir/install_config.sh"
echo "HTDOCS_DIR=\"$HTDOCS_DIR\"" >> "$tmp_dir/install_config.sh"

( cd "$tmp_dir" ; tar -zcf deploy.tar.gz * )

ssh_run="ssh -o BatchMode=yes $DEST_SERVER_USER@$DEST_SERVER_HOST"
liquibase_run="liquibase/liquibase --url=jdbc:mysql://$DB_HOST/$DB_NAME --username=$DB_USER --password=$DB_PASS --changeLogFile=etc/db.changelog.xml"

$ssh_run "rm -rf \"$DEST_SERVER_TEMP\" && mkdir -p \"$DEST_SERVER_TEMP\""
did_ssh=1
scp -B "$tmp_dir/deploy.tar.gz" $DEST_SERVER_USER@$DEST_SERVER_HOST:$DEST_SERVER_TEMP/
$ssh_run "cd \"$DEST_SERVER_TEMP\" && tar -zxf deploy.tar.gz && ./install.sh"
$liquibase_run update
$ssh_run "cd $HTDOCS_DIR && mv -fv index.php.orig index.php"

php scripts/smoke_test.php $DEST_SERVER_HTTP

echo "Done."

