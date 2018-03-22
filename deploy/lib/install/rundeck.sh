#!/usr/bin/env bash
rpm -Uvh http://repo.rundeck.org/latest.rpm
make_yum_proxy_friendly.sh
yum install -y --nogpgcheck rundeck rundeck-cli