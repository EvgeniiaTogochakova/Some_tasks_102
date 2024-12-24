#!/bin/bash
# check_events.sh
PHP_PATH="/usr/bin/php"
APP_DIR="/home/jane/php-around/seminar1"
cd $APP_DIR
$PHP_PATH runner -c handle_events
