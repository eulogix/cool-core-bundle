# build:
# docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_pdi_image .
#
# to run the image (from app root):
# docker run -ti --cap-add SYS_ADMIN --security-opt seccomp:unconfined -p 8080:8080 your_app_pdi_image:latest

FROM cool_base_php_image:latest

VOLUME ["/app"]

COPY container-files /
COPY lib/common /
COPY lib/pdi /
COPY lib /tmp/install_lib

COPY pdi.zip /opt/

RUN /tmp/install_lib/install/java.sh \
&& /tmp/install_lib/install/maven.sh \
&& /tmp/install_lib/install/pdi.sh \
&& rm -rf /tmp/install_lib

ADD ./etc/kettle_home/.kettle/* /home/www-data/.kettle/
ADD ./etc/carte/kettle.pwd /opt/data-integration/pwd/
ADD ./etc/carte/single-master-config.xml /opt/data-integration/pwd/carte-config.xml

RUN systemctl enable pdi_image_init.service \
&& mkdir /var/log/pentaho \
&& chown www-data:www-data /var/log/pentaho \
&& chown www-data:www-data -R /home/www-data \
&& chown www-data:www-data -R /opt/data-integration/pwd/

ENV PATH="/opt/data-integration/:${PATH}"

EXPOSE 8080
