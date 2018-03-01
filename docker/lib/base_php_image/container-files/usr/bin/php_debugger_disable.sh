#!/usr/bin/env bash
cat /usr/bin/xdebug_debugger_off.ini >/etc/php.d/15-xdebug.ini
service php-fpm restart