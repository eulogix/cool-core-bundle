#!/usr/bin/env bash
yum --enablerepo=remi-php71 install -y --nogpgcheck php-fpm
sed -i -E 's/listen =.+?$/listen = \/var\/run\/php-fpm\/php-fpm.sock/'  /etc/php-fpm.d/www.conf
sed -i -E 's/apache/www-data/'  /etc/php-fpm.d/www.conf
chown root:www-data /var/run/php-fpm
yum install -y --nogpgcheck nginx
sed -i -E 's/80 default_server/80/'  /etc/nginx/nginx.conf
sed -i -E 's/^user nginx;/user www-data;/'  /etc/nginx/nginx.conf