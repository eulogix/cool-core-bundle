# build:
# docker build --network=host --build-arg http_proxy=http://192.168.0.107:3128 -t cool_base_pg_image .
#
# to run the image (from this file folder):
# make sure that folder /opt/pdata/pg_hams has gid:uid set to 26:26 and is mounted on a linux FS
# docker run -ti --cap-add SYS_ADMIN --security-opt seccomp:unconfined -v /opt/pdata/pg:/var/lib/pgsql/data cool_base_pg_image

FROM cool_base_php_image:latest

RUN yum localinstall -y --nogpgcheck https://download.postgresql.org/pub/repos/yum/9.6/redhat/rhel-7-x86_64/pgdg-centos96-9.6-3.noarch.rpm \
&& yum --enablerepo=pgdg96 install -y --nogpgcheck dal-libs geos libgeotiff pg_activity pg_top96 plv8_96 postgis2_96 postgis2_96-client postgresql96 postgresql96-contrib postgresql-jdbc postgis-jdbc postgresql96-libs postgresql96-plpython postgresql96-server proj python-psycopg2

RUN sed -i -e "s|/var/lib/pgsql/9.6/data|/var/lib/pgsql/data|" /usr/lib/systemd/system/postgresql-9.6.service

COPY container-files /
RUN systemctl enable pg_image_init.service

VOLUME [ "/var/lib/pgsql/data"]
VOLUME [ "/app" ]

EXPOSE 5432
