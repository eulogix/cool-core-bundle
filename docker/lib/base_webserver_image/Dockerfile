# build:
# docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_webserver_image .
#
# to run the image (from app root):
# docker run -ti --cap-add SYS_ADMIN --security-opt seccomp:unconfined --mount type=bind,source="$(pwd)",target=/app -p 90:80 your_app_image:latest

FROM cool_base_php_image:latest

## base php machine end

RUN sed -i -E 's/listen =.+?$/listen = \/var\/run\/php-fpm\/php-fpm.sock/'  /etc/php-fpm.d/www.conf \
&& sed -i -E 's/apache/www-data/'  /etc/php-fpm.d/www.conf \
&& chown root:www-data /var/run/php-fpm

RUN yum install -y --nogpgcheck git nginx java

ADD ./etc/php-fpm/* /etc/php-fpm.d/

RUN sed -i -E 's/80 default_server/80/'  /etc/nginx/nginx.conf \
&& sed -i -E 's/^user nginx;/user www-data;/'  /etc/nginx/nginx.conf
ADD ./etc/nginx/* /etc/nginx/conf.d/

RUN chkconfig php-fpm on \
&& chkconfig nginx on

ADD ./init_script.sh /usr/bin/
RUN chmod +x /usr/bin/init_script.sh

ADD ./image_init.service /etc/systemd/system/
RUN systemctl enable image_init.service

VOLUME ["/app"]

EXPOSE 80