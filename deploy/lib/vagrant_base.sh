#!/usr/bin/env bash
setenforce 0

/tmp/install_lib/env.sh

mkdir /app
chown 33:33 /app

mkdir /app_data
chown 33:33 /app_data

mkdir /shared
mkdir /shared/xdebug_output
mkdir /shared/tmp
mkdir /shared/log
chmod 777 -R /shared

mkdir /opt/wget_cache
chmod 666 /opt/wget_cache

/tmp/install_lib/install/squid.sh

/tmp/install_lib/install/cifs.sh
fstab_line="$1    /app   cifs  _netdev,username=$2,password=$3,dir_mode=0755,file_mode=0755,uid=33,gid=33 0 0"
echo "$fstab_line" >>/etc/fstab
fstab_line="$9    /app_data   cifs  _netdev,username=$2,password=$3,dir_mode=0755,file_mode=0755,uid=33,gid=33 0 0"
echo "$fstab_line" >>/etc/fstab
fstab_line="$4    /var/spool/squid   cifs  _netdev,username=$2,password=$3,dir_mode=0755,file_mode=0666,uid=23,gid=23 0 0"
echo "$fstab_line" >>/etc/fstab
fstab_line="$5   /opt/wget_cache   cifs  _netdev,username=$2,password=$3,dir_mode=0755,file_mode=0666,uid=33,gid=33 0 0"
echo "$fstab_line" >>/etc/fstab
mount -a

sed -i -E "s/172\.17\.0\.1/$7/"  /usr/bin/xdebug_debugger_off.ini
sed -i -E "s/172\.17\.0\.1/$7/"  /usr/bin/xdebug_profiler_off.ini
sed -i -E "s/172\.17\.0\.1/$7/"  /usr/bin/xdebug_profiler_on.ini

sed -i -E "s/_localvm_/$6/"  /etc/profile.d/custom.sh

/tmp/install_lib/sshd_conf.sh
service sshd restart

chkconfig squid on
service squid start