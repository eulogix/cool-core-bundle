# build:
# docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_pdi_image .
#
# to run the image (from app root):
# docker run -ti --cap-add SYS_ADMIN --security-opt seccomp:unconfined -p 8080:8080 your_app_pdi_image:latest

FROM cool_base_php_image:latest

WORKDIR /opt
# RUN wget https://sourceforge.net/projects/pentaho/files/Data%20Integration/7.1/pdi-ce-7.1.0.0-12.zip/download -O pdi.zip
RUN wget https://iweb.dl.sourceforge.net/project/pentaho/Data%20Integration/7.1/pdi-ce-7.1.0.0-12.zip -O pdi.zip

RUN unzip pdi.zip

RUN yum install -y --nogpgcheck java maven \
&& git clone https://github.com/eulogix/cool-kettle-integration.git \
&& git clone https://github.com/eulogix/kettle-plugins.git

COPY container-files /

RUN recompile_plugins.sh

RUN rm -rf /opt/data-integration/plugins/plugins_compiled \
&& cp -R /opt/plugins_compiled data-integration/plugins/

ADD ./etc/kettle_home/.kettle/* /home/www-data/.kettle/
RUN chown www-data:www-data -R /home/www-data \
&& chown www-data:www-data -R /opt/*

ADD ./etc/carte/kettle.pwd /opt/data-integration/pwd/
ADD ./etc/carte/single-master-config.xml /opt/data-integration/pwd/carte-config.xml

RUN mkdir /var/log/pentaho \
&& chown www-data:www-data /var/log/pentaho

ENV PATH="/opt/data-integration/:${PATH}"

RUN systemctl enable pdi_image_init.service

VOLUME [ "/app" ]

EXPOSE 8080
