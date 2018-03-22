#!/usr/bin/env bash
rpm -U http://opensource.wandisco.com/centos/7/git/x86_64/wandisco-git-release-7-2.noarch.rpm
make_yum_proxy_friendly.sh
yum install -y --nogpgcheck git