# build:
#
# this image provides a base centos7 systemd enabled image meant to be extended
# to run the image (from app root):
# docker run -ti --cap-add SYS_ADMIN --security-opt seccomp:unconfined --mount type=bind,source="$(pwd)",target=/app cool_base_php_image:latest

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

COPY container-files /
COPY lib/common /
COPY lib /tmp/install_lib

RUN /tmp/install_lib/env.sh \
&& /tmp/install_lib/install/common.sh \
&& /tmp/install_lib/install/pg_client.sh \
&& /tmp/install_lib/install/git.sh \
&& /tmp/install_lib/install/php.sh \
&& /tmp/install_lib/install/composer.sh \
&& rm -rf /tmp/install_lib

VOLUME [ "/shared" ]

ENV DEBUGGER_ENABLED=0
ENV PROFILER_ENABLED=0
ENV XDEBUG_CONFIG="remote_host=172.17.0.1 idekey=PHPSTORM"
ENV PHP_IDE_CONFIG="serverName=docker"

ENTRYPOINT ["/usr/bin/image_init.sh"]