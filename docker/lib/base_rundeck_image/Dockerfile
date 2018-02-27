# build:
# docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_rundeck_image .
#
# to run the image (from app root):
# docker run -ti --cap-add SYS_ADMIN --security-opt seccomp:unconfined --mount type=bind,source="$(pwd)",target=/app -p 90:80 your_app_rd_image:latest

FROM cool_base_php_image:latest

# rd will use this to launch console jobs
VOLUME ["/app"]

RUN yum install -y --nogpgcheck java \
&& rpm -Uvh http://repo.rundeck.org/latest.rpm \
&& yum install -y --nogpgcheck rundeck rundeck-cli \
&& chkconfig rundeckd on

ADD ./etc/rundeck/* /etc/rundeck/
ADD ./etc/profile.sh /etc/profile.d/base_rundeck_image_env_vars.sh
ADD ./init_script.sh /usr/bin/base_rundeck_image_init.sh
ADD ./build_token.php /usr/bin/
ADD ./import_jobs.sh /usr/bin/

ADD ./image_init.service /etc/systemd/system/base_rundeck_image_init.service
RUN systemctl enable base_rundeck_image_init.service

EXPOSE 4440