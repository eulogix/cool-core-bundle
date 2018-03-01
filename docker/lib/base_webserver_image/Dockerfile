# build:
# docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_webserver_image .
#
# to run the image (from app root):
# docker run -ti --mount type=bind,source="$(pwd)",target=/app -p 80:80 your_app_image:latest

FROM cool_base_php_image:latest

RUN sed -i -E 's/listen =.+?$/listen = \/var\/run\/php-fpm\/php-fpm.sock/'  /etc/php-fpm.d/www.conf \
&& sed -i -E 's/apache/www-data/'  /etc/php-fpm.d/www.conf \
&& chown root:www-data /var/run/php-fpm \
&& yum install -y --nogpgcheck nginx java \
&& sed -i -E 's/80 default_server/80/'  /etc/nginx/nginx.conf \
&& sed -i -E 's/^user nginx;/user www-data;/'  /etc/nginx/nginx.conf

VOLUME ["/app"]

COPY container-files /

RUN systemctl enable webserver_image_init.service \
&& systemctl enable php-fpm.service \
&& systemctl enable nginx.service

EXPOSE 80