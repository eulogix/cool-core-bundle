rm -f /etc/php.d/xdebug.ini
cp /usr/bin/xdebug_debugger_off.ini /etc/php.d/xdebug.ini
service php-fpm restart
service nginx restart
