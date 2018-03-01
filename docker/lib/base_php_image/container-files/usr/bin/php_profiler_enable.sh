#!/usr/bin/env bash
cat /usr/bin/xdebug_profiler_on.ini >/etc/php.d/15-xdebug.ini
rm -f /shared/xdebug_output/* || true
service php-fpm restart