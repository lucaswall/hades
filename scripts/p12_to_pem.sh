#!/bin/bash
set -ex
openssl pkcs12 -in $1 -out $2 -nodes
