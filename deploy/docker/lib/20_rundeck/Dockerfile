# build:
# docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_rundeck_image .
#
# to run the image (from app root):
# docker run -ti --cap-add SYS_ADMIN --security-opt seccomp:unconfined --mount type=bind,source="$(pwd)",target=/app -p 90:80 your_app_rd_image:latest

FROM cool_base_php_image:latest

# rd will use this to launch console jobs
# and to instance the wamp server and db listener
VOLUME ["/app"]

COPY lib/common /
COPY lib /tmp/install_lib

RUN /tmp/install_lib/install/java.sh \
&& /tmp/install_lib/install/rundeck.sh

ENV JAVA_HOME=/usr/lib/jvm/jre
ENV RD_PASSWORD=admin
ENV RD_URL=http://docker-host.devel:4440
ENV RD_USER=admin
ENV RD_PASSWORD=admin

COPY container-files /
COPY lib/rundeck /
RUN systemctl enable rundeck_image_init.service \
&& chkconfig rundeckd on

EXPOSE 4440