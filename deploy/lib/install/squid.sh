#!/usr/bin/env bash
yum install -y --nogpgcheck squid
sed -i -E 's/.*?cache_dir ufs.+?/cache_dir ufs \/var\/spool\/squid 10000 16 256/'  /etc/squid/squid.conf
echo "maximum_object_size 1000 MB" >>/etc/squid/squid.conf