rm -f /etc/php.d/xdebug.ini
cp /usr/bin/xdebug_profiler_on.ini /etc/php.d/xdebug.ini
rm -f /shared/xdebug_output/*
service php-fpm restart
service nginx restart
