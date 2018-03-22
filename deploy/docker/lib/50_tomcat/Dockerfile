# build:
# docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_tomcat_image .
#
# to run the image (from app root):
# docker run -ti --cap-add SYS_ADMIN --security-opt seccomp:unconfined -p 8080:8080 your_app_tomcat_image:latest

FROM cool_base_php_image:latest

COPY lib/common /
COPY lib/tomcat/files /
COPY lib /tmp/install_lib

RUN mkdir /tmp/bloat
COPY activiti-5.16.3.zip /tmp/bloat
COPY javabridge.zip /tmp/bloat

RUN /tmp/install_lib/install/java.sh \
&& /tmp/install_lib/install/maven.sh \
&& /tmp/install_lib/install/tomcat.sh \
&& /tmp/install_lib/install/activiti.sh \
&& /bin/cp -f /tmp/install_lib/tomcat/tomcat-users.xml /etc/tomcat/tomcat-users.xml \
&& /bin/cp -f /tmp/install_lib/tomcat/activiti-context.xml /var/lib/tomcat/webapps/activiti-rest/WEB-INF/classes/activiti-context.xml
&& /bin/cp -f /tmp/install_lib/tomcat/db.properties /var/lib/tomcat/webapps/activiti-rest/WEB-INF/classes/db.properties

COPY container-files /

RUN chown tomcat:tomcat -R /var/lib/tomcat/webapps \
&& systemctl enable tomcat_image_init.service \
&& systemctl enable tomcat.service \
&& rm -rf /tmp/install_lib

EXPOSE 8080