#!/usr/bin/env bash
rpm -U http://rpms.remirepo.net/enterprise/remi-release-7.rpm
make_yum_proxy_friendly.sh
yum --enablerepo=remi-php71 install -y --nogpgcheck php php-bcmath php-cli php-common php-gd php-mbstring php-mcrypt php-mysqlnd php-opcache php-pdo php-pecl-apcu php-pecl-apcu-bc php-pecl-igbinary php-pecl-jsonc php-pecl-memcache php-pecl-memcached php-pecl-msgpack php-pecl-sqlite php-pecl-xdebug php-pecl-zip php-pecl-zmq php-pgsql php-process php-soap php-xml php-pecl-http php-pecl-mailparse


