# build:
# docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_webserver_image .
#
# to run the image (from app root):
# docker run -ti --cap-add SYS_ADMIN --security-opt seccomp:unconfined --mount type=bind,source="$(pwd)",target=/app -v /sys/fs/cgroup:/sys/fs/cgroup:ro base_webserver_image:latest

FROM cool_base_php_image:latest

VOLUME ["/app"]

COPY container-files /
COPY lib/common /
COPY lib/webserver /
COPY lib /tmp/install_lib

RUN /tmp/install_lib/install/java.sh \
&& /tmp/install_lib/install/nginx.sh \
&& rm -rf /tmp/install_lib

RUN systemctl enable webserver_image_init.service \
&& systemctl enable php-fpm.service \
&& systemctl enable nginx.service

ENV JAVA_HOME=/usr/lib/jvm/jre

EXPOSE 80