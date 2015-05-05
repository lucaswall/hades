#!/bin/bash

if [ "z$BRANCH" = "z" ]; then
	echo "Error! Env var BRANCH not set!"
	exit 1
fi

c_dir="`dirname $0`"
ver_file="`cd $c_dir && pwd`/../model/HadesBuild.php"
bk_file="${ver_file}.bk"

function finish {
	if [ -f "$bk_file" ]; then
		rm -f "$ver_file"
		mv "$bk_file" "$ver_file"
	fi
	rm -f "$bk_file"
}
trap finish EXIT

set -ex
cd "`dirname $0`/../"

BUILD_NUMBER="${BUILD_NUMBER:-0}"
BUILD_ID="${BUILD_ID:-local-build}"

if [ -d ".private" ]; then
	( cd .private && cp -rfv * .. )
fi

cp "$ver_file" "$bk_file"
sed -i -e "s/BUILD_NUMBER = .*/BUILD_NUMBER = \"$BUILD_NUMBER\";/" "$ver_file"
sed -i -e "s/BUILD_ID = .*/BUILD_ID = \"$BUILD_ID\";/" "$ver_file"
sed -i -e "s/BRANCH = .*/BRANCH = \"$BRANCH\";/" "$ver_file"

./composer install
scripts/run_full_tests.sh

tar --exclude='server.log' --exclude='hades-*.tar.gz' --exclude='*.bk' -zcf hades-$BRANCH-$BUILD_NUMBER.tar.gz .gitignore *

