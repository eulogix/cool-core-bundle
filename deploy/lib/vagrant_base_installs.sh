#!/usr/bin/env bash
/tmp/install_lib/install/common.sh
/tmp/install_lib/install/git.sh
/tmp/install_lib/install/php.sh
/tmp/install_lib/install/composer.sh
/tmp/install_lib/install/node.sh

service firewalld stop
chkconfig firewalld off
