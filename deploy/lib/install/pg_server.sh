#!/usr/bin/env bash
yum localinstall -y --nogpgcheck https://download.postgresql.org/pub/repos/yum/9.6/redhat/rhel-7-x86_64/pgdg-centos96-9.6-3.noarch.rpm
make_yum_proxy_friendly.sh
yum --enablerepo=pgdg96 install -y --nogpgcheck dal-libs geos libgeotiff pg_activity pg_top96 plv8_96 postgis2_96 postgis2_96-client postgresql96 postgresql96-contrib postgresql-jdbc postgis-jdbc postgresql96-libs postgresql96-plpython postgresql96-server proj python-psycopg2
sed -i -e "s|/var/lib/pgsql/9.6/data|$PGDATA|" /usr/lib/systemd/system/postgresql-9.6.service
