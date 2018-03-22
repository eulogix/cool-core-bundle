#!/usr/bin/env bash
yum localinstall -y --nogpgcheck https://download.postgresql.org/pub/repos/yum/9.6/redhat/rhel-7-x86_64/pgdg-centos96-9.6-3.noarch.rpm
make_yum_proxy_friendly.sh
yum --enablerepo=pgdg96 install -y --nogpgcheck postgresql96