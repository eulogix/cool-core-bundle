# build:
# docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_pdi_image .
#
# to run the image (from app root):
# docker run -ti --cap-add SYS_ADMIN --security-opt seccomp:unconfined -p 8080:8080 your_app_pdi_image:latest

FROM cool_base_php_image:latest

WORKDIR /opt
RUN wget https://sourceforge.net/projects/pentaho/files/Data%20Integration/7.1/pdi-ce-7.1.0.0-12.zip/download -O pdi.zip

RUN unzip pdi.zip

VOLUME /root/.m2

RUN yum install -y --nogpgcheck git maven

RUN git clone https://github.com/eulogix/cool-kettle-integration.git \
&& git clone https://github.com/eulogix/kettle-plugins.git

ADD ./scripts/recompile_plugins.sh /opt

WORKDIR /opt

RUN ./recompile_plugins.sh

RUN rm -rf data-integration/plugins/plugins_compiled \
&& cp -R plugins_compiled data-integration/plugins/

ADD ./etc/base_pdi_image_profile.sh /etc/profile.d/
ADD ./etc/kettle_home/.kettle/* /home/www-data/.kettle/
RUN chown www-data:www-data -R /home/www-data

RUN chown www-data:www-data -R *

ADD ./etc/carte/kettle.pwd /opt/data-integration/pwd/
ADD ./etc/carte/single-master-config.xml /opt/data-integration/pwd/carte-config.xml

ADD ./init_script.sh /usr/bin/base_pdi_image_init.sh
ADD ./image_init.service /etc/systemd/system/base_pdi_image_init.service
RUN systemctl enable base_pdi_image_init.service

RUN mkdir /var/log/pentaho \
&& chown www-data:www-data /var/log/pentaho

EXPOSE 8080
