# build:
# docker build --network=host --build-arg http_proxy=http://192.168.0.107:3128 -t cool_base_pg_image .
#
# to run the image (from this file folder):
# make sure that folder /opt/pdata/pg_hams has gid:uid set to 26:26 and is mounted on a linux FS
# docker run -ti --cap-add SYS_ADMIN --security-opt seccomp:unconfined -v /opt/pdata/pg:/var/lib/pgsql/data cool_base_pg_image

FROM centos:latest

# enable systemd
ENV container docker
RUN (cd /lib/systemd/system/sysinit.target.wants/; for i in *; do [ $i == \
systemd-tmpfiles-setup.service ] || rm -f $i; done); \
rm -f /lib/systemd/system/multi-user.target.wants/*;\
rm -f /etc/systemd/system/*.wants/*;\
rm -f /lib/systemd/system/local-fs.target.wants/*; \
rm -f /lib/systemd/system/sockets.target.wants/*udev*; \
rm -f /lib/systemd/system/sockets.target.wants/*initctl*; \
rm -f /lib/systemd/system/basic.target.wants/*;\
rm -f /lib/systemd/system/anaconda.target.wants/*;
VOLUME [ "/sys/fs/cgroup" ]

ENV PGDATA="/var/lib/pgsql/data"

# set root psw
RUN echo "root:root" | chpasswd

# basic binaries and libs
RUN yum install -y --nogpgcheck epel-release sudo initscripts deltarpm net-tools wget zip unzip

RUN yum localinstall -y --nogpgcheck https://download.postgresql.org/pub/repos/yum/9.6/redhat/rhel-7-x86_64/pgdg-centos96-9.6-3.noarch.rpm

RUN yum --enablerepo=pgdg96 install -y --nogpgcheck dal-libs geos libgeotiff pg_activity pg_top96 plv8_96 postgis2_96 postgis2_96-client postgresql96 postgresql96-contrib postgresql-jdbc postgis-jdbc postgresql96-libs postgresql96-plpython postgresql96-server proj python-psycopg2

RUN sed -i -e "s|/var/lib/pgsql/9.6/data|$PGDATA|" /usr/lib/systemd/system/postgresql-9.6.service

ADD ./init_script.sh /usr/bin/
ADD ./custom_init_script.sh /usr/bin/
RUN chmod +x /usr/bin/init_script.sh \
&& chmod +x /usr/bin/custom_init_script.sh

RUN echo "export PGDATA=$PGDATA" >> /etc/profile.d/base_pg_image_env_vars.sh

ADD ./image_init.service /etc/systemd/system/
RUN systemctl enable image_init.service

ADD ./sql/* /opt/sql/
ADD ./etc/* /opt/etc_base/

# Without this, init won't start the enabled services and exec'ing and starting
# them reports "Failed to get D-Bus connection: Operation not permitted".
VOLUME /run /tmp

# Don't know if it's possible to run services without starting this
CMD ["/usr/sbin/init"]

EXPOSE 5432
