# build:
# docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_php_image .
#
# this image provides a base centos7 systemd enabled image meant to be extended

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

# set root psw
RUN echo "root:root" | chpasswd

# add www-data user and set uids and gids
RUN useradd www-data \
&& groupmod -g 33 www-data \
&& usermod -u 33 www-data

# basic binaries and libs
RUN yum install -y --nogpgcheck sudo initscripts deltarpm net-tools wget zip unzip ImageMagick

WORKDIR /tmp

RUN wget https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
RUN wget http://rpms.remirepo.net/enterprise/remi-release-7.rpm
RUN rpm -Uvh epel-release-latest-7.noarch.rpm
RUN rpm -Uvh remi-release-7.rpm

RUN rpm -U http://opensource.wandisco.com/centos/7/git/x86_64/wandisco-git-release-7-2.noarch.rpm

RUN yum --enablerepo=remi-php71 install -y --nogpgcheck php php-bcmath php-cli php-common php-gd php-mbstring php-mcrypt php-mysqlnd php-opcache php-pdo php-pecl-apcu php-pecl-apcu-bc php-pecl-igbinary php-pecl-jsonc php-pecl-memcache php-pecl-memcached php-pecl-msgpack php-pecl-sqlite php-pecl-xdebug php-pecl-zip php-pecl-zmq php-pgsql php-process php-soap php-xml php-fpm php-pecl-http php-pecl-mailparse

ADD ./php/* /etc/php.d/

RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/master/web/installer -O - -q | php -- --quiet \
&& mv composer.phar /usr/bin/composer \
&& chmod +x /usr/bin/composer

ADD ./etc/base_php_image_profile.sh /etc/profile.d/

ADD ./scripts/* /usr/bin/

RUN mkdir /shared \
&& mkdir /shared/xdebug_output \
&& mkdir /shared/tmp \
&& chmod 777 -R /shared
VOLUME [ "/shared" ]

# Sometimes ithout this, init won't start the enabled services and exec'ing and starting
# them reports "Failed to get D-Bus connection: Operation not permitted".
# VOLUME /run /tmp

# Don't know if it's possible to run services without starting this
CMD ["/usr/sbin/init"]