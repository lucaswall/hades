#!/bin/bash
set -ex
cd "`dirname $0`/.."
./composer install
scripts/run_full_tests.sh
