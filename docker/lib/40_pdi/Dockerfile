# build:
# docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_pdi_image .
#
# to run the image (from app root):
# docker run -ti --cap-add SYS_ADMIN --security-opt seccomp:unconfined -p 8080:8080 your_app_pdi_image:latest

FROM cool_base_php_image:latest

COPY container-files /

WORKDIR /opt

COPY pdi.zip /opt/
RUN unzip pdi.zip \
&& rm -f pdi.zip \
&& yum install -y --nogpgcheck java maven \
&& git clone https://github.com/eulogix/cool-kettle-integration.git \
&& git clone https://github.com/eulogix/kettle-plugins.git \
&& recompile_plugins.sh \
&& rm -rf /opt/data-integration/plugins/plugins_compiled \
&& cp -R /opt/plugins_compiled data-integration/plugins/ \
&& chown www-data:www-data -R /opt/* \
&& systemctl enable pdi_image_init.service

ADD ./etc/kettle_home/.kettle/* /home/www-data/.kettle/
ADD ./etc/carte/kettle.pwd /opt/data-integration/pwd/
ADD ./etc/carte/single-master-config.xml /opt/data-integration/pwd/carte-config.xml

RUN mkdir /var/log/pentaho \
&& chown www-data:www-data /var/log/pentaho \
&& chown www-data:www-data -R /home/www-data \
&& chown www-data:www-data -R /opt/data-integration/pwd/

ENV PATH="/opt/data-integration/:${PATH}"

VOLUME [ "/app" ]

EXPOSE 8080
