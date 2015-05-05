#!/bin/bash

set -ex

cd "`dirname $0`"
. ./install_config.sh

ls -al

mkdir -p "$HTDOCS_DIR"
mkdir -p "$LIBS_DIR"

cp htdocs/index.php "$HTDOCS_DIR/"
rsync -crat --delete libs/vendor/ "$LIBS_DIR/vendor"
rsync -crat --delete libs/model/ "$LIBS_DIR/model"
rsync -crat --delete libs/config/ "$LIBS_DIR/config"
rsync -crat --delete libs/controller/ "$LIBS_DIR/controller"
rsync -crat --delete htdocs/ "$HTDOCS_DIR"

