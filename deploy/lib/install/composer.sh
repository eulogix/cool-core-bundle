#!/usr/bin/env bash
wget https://raw.githubusercontent.com/composer/getcomposer.org/master/web/installer -O - -q | php -- --quiet
mv composer.phar /usr/bin/composer
chmod +x /usr/bin/composer
