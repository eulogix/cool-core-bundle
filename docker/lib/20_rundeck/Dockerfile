# build:
# docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_rundeck_image .
#
# to run the image (from app root):
# docker run -ti --cap-add SYS_ADMIN --security-opt seccomp:unconfined --mount type=bind,source="$(pwd)",target=/app -p 90:80 your_app_rd_image:latest

FROM cool_base_php_image:latest

RUN yum install -y --nogpgcheck java

RUN rpm -Uvh http://repo.rundeck.org/latest.rpm \
&& make_yum_proxy_friendly.sh \
&& yum install -y --nogpgcheck rundeck rundeck-cli

# rd will use this to launch console jobs
# and to instance the wamp server and db listener
VOLUME ["/app"]

ENV JAVA_HOME=/usr/lib/jvm/jre
ENV RD_PASSWORD=admin
ENV RD_URL=http://docker-host.devel:4440
ENV RD_USER=admin
ENV RD_PASSWORD=admin

COPY container-files /
RUN systemctl enable rundeck_image_init.service \
&& chkconfig rundeckd on

EXPOSE 4440