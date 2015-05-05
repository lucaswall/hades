
# Hades Mobile Backend

A general purpose mobile backend server written in PHP which includes:

* Push notifications on iOS/Android.

## Directory Structure

Install on web server:

* vendor
* model
* controller
* config
* public_html

Directory "public_html" should be htdoc root.

## Database

Use liquibase to properly setup database. See 'scripts/update_database.sh'.
