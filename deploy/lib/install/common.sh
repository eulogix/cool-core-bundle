#!/usr/bin/env bash
rpm -U https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
make_yum_proxy_friendly.sh
yum install -y --nogpgcheck sudo initscripts net-tools wget zip unzip crontabs htop iotop telnet ImageMagick