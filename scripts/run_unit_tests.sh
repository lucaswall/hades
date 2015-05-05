#!/bin/sh
cd "`dirname $0`/.."
vendor/bin/phpunit tests/unit
