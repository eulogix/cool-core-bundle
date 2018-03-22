# build:
# docker build --network=host --build-arg http_proxy=http://192.168.0.107:3128 -t cool_base_pg_image .
#
# to run the image (from this file folder):
# make sure that folder /opt/pdata/pg_hams has gid:uid set to 26:26 and is mounted on a linux FS
# docker run -ti --cap-add SYS_ADMIN --security-opt seccomp:unconfined -v /opt/pdata/pg:/var/lib/pgsql/data cool_base_pg_image

FROM cool_base_php_image:latest

ENV PGDATA=/shared/pg_data

VOLUME ["/app"]

COPY lib/common /
COPY lib /tmp/install_lib

RUN /tmp/install_lib/install/pg_server.sh \
&& rm -rf /tmp/install_lib

COPY container-files /
COPY lib/pg /
RUN systemctl enable pg_image_init.service

EXPOSE 5432
