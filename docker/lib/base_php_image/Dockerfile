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

WORKDIR /tmp

RUN echo "root:root" | chpasswd \

&& useradd www-data \
&& groupmod -g 33 www-data \
&& usermod -u 33 www-data \
&& rpm -U http://opensource.wandisco.com/centos/7/git/x86_64/wandisco-git-release-7-2.noarch.rpm \
&& yum install -y --nogpgcheck sudo initscripts deltarpm net-tools wget zip unzip ImageMagick crontabs git \
&& wget https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm \
&& rpm -Uvh epel-release-latest-7.noarch.rpm \
&& wget http://rpms.remirepo.net/enterprise/remi-release-7.rpm \
&& rpm -Uvh remi-release-7.rpm \
&& yum --enablerepo=remi-php71 install -y --nogpgcheck php php-bcmath php-cli php-common php-gd php-mbstring php-mcrypt php-mysqlnd php-opcache php-pdo php-pecl-apcu php-pecl-apcu-bc php-pecl-igbinary php-pecl-jsonc php-pecl-memcache php-pecl-memcached php-pecl-msgpack php-pecl-sqlite php-pecl-xdebug php-pecl-zip php-pecl-zmq php-pgsql php-process php-soap php-xml php-fpm php-pecl-http php-pecl-mailparse \
&& wget https://raw.githubusercontent.com/composer/getcomposer.org/master/web/installer -O - -q | php -- --quiet \
&& mv composer.phar /usr/bin/composer \
&& chmod +x /usr/bin/composer \
&& mkdir /shared \
&& mkdir /shared/xdebug_output \
&& mkdir /shared/tmp \
&& chmod 777 -R /shared

VOLUME [ "/shared" ]

ENV DEBUGGER_ENABLED=1
ENV PROFILER_ENABLED=0
ENV XDEBUG_CONFIG="remote_host=172.17.0.1 idekey=PHPSTORM"
ENV PHP_IDE_CONFIG="serverName=docker"

COPY container-files /

ENTRYPOINT ["/usr/bin/image_init.sh"]