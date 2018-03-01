# build:
# docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_tomcat_image .
#
# to run the image (from app root):
# docker run -ti --cap-add SYS_ADMIN --security-opt seccomp:unconfined -p 8080:8080 your_app_tomcat_image:latest

FROM cool_base_php_image:latest

RUN yum install -y --nogpgcheck java
RUN yum install -y --nogpgcheck tomcat tomcat-admin-webapps tomcat-el-2.2-api tomcat-jsp-2.2-api tomcat-lib tomcat-servlet-3.0-api tomcat-webapps

WORKDIR /tmp

RUN wget https://github.com/Activiti/Activiti/releases/download/activiti-5.16.3/activiti-5.16.3.zip \
&& wget https://jdbc.postgresql.org/download/postgresql-42.2.1.jar

RUN unzip activiti-5.16.3.zip \
&& mv activiti-5.16.3/wars/activiti-rest.war /var/lib/tomcat/webapps/ \
&& cd /var/lib/tomcat/webapps \
&& unzip activiti-rest.war -d activiti-rest \
&& cp /tmp/postgresql-42.2.1.jar activiti-rest/WEB-INF/lib/

ADD ./etc/tomcat/tomcat-users.xml /etc/tomcat/tomcat-users.xml
ADD ./etc/tomcat/db.properties /var/lib/tomcat/webapps/activiti-rest/WEB-INF/classes/db.properties

RUN yum localinstall -y --nogpgcheck https://download.postgresql.org/pub/repos/yum/9.6/redhat/rhel-7-x86_64/pgdg-centos96-9.6-3.noarch.rpm
RUN yum --enablerepo=pgdg96 install -y --nogpgcheck postgresql96

WORKDIR /root
RUN yum install -y --nogpgcheck git maven \
&& git clone https://github.com/eulogix/cool-activiti-integration.git \
&& cd cool-activiti-integration \
&& mvn clean install \
&& cp target/uber*.jar /var/lib/tomcat/webapps/activiti-rest/WEB-INF/lib/

ADD ./etc/activiti/activiti-context.xml /var/lib/tomcat/webapps/activiti-rest/WEB-INF/classes/activiti-context.xml

# RUN wget http://sourceforge.net/projects/php-java-bridge/files/Binary%20package/php-java-bridge_7.2.1/php-java-bridge_7.2.1_documentation.zip/download -O javabridge.zip \
RUN wget https://ufpr.dl.sourceforge.net/project/php-java-bridge/Binary%20package/php-java-bridge_7.2.1/php-java-bridge_7.2.1_documentation.zip -O javabridge.zip \
&& unzip javabridge.zip \
&& mv JavaBridge.war /var/lib/tomcat/webapps/

WORKDIR /var/lib/tomcat/webapps

RUN chown tomcat:tomcat * -R

COPY container-files /

RUN systemctl enable tomcat_image_init.service \
&& systemctl enable tomcat.service

EXPOSE 8080