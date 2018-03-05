# build:
# docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_tomcat_image .
#
# to run the image (from app root):
# docker run -ti --cap-add SYS_ADMIN --security-opt seccomp:unconfined -p 8080:8080 your_app_tomcat_image:latest

FROM cool_base_php_image:latest

RUN yum install -y --nogpgcheck java tomcat tomcat-admin-webapps tomcat-el-2.2-api tomcat-jsp-2.2-api tomcat-lib tomcat-servlet-3.0-api tomcat-webapps git maven

RUN mkdir /tmp/bloat
WORKDIR /tmp/bloat

COPY activiti-5.16.3.zip /tmp/bloat
COPY javabridge.zip /tmp/bloat

RUN wget https://jdbc.postgresql.org/download/postgresql-42.2.1.jar \
&& unzip activiti-5.16.3.zip \
&& mv activiti-5.16.3/wars/activiti-rest.war /var/lib/tomcat/webapps/ \
&& unzip javabridge.zip \
&& mv JavaBridge.war /var/lib/tomcat/webapps/ \
&& cd /var/lib/tomcat/webapps \
&& unzip activiti-rest.war -d activiti-rest \
&& unzip -o JavaBridge.war -d JavaBridge \
&& rm -f *.war \
&& cp /tmp/bloat/postgresql-42.2.1.jar activiti-rest/WEB-INF/lib/ \
&& cd /tmp/bloat \
&& git clone https://github.com/eulogix/cool-activiti-integration.git \
&& cd cool-activiti-integration \
&& mvn clean install \
&& cp target/uber*.jar /var/lib/tomcat/webapps/activiti-rest/WEB-INF/lib/

ADD ./etc/tomcat/tomcat-users.xml /etc/tomcat/tomcat-users.xml
ADD ./etc/tomcat/db.properties /var/lib/tomcat/webapps/activiti-rest/WEB-INF/classes/db.properties

ADD ./etc/activiti/activiti-context.xml /var/lib/tomcat/webapps/activiti-rest/WEB-INF/classes/activiti-context.xml

RUN chown tomcat:tomcat /var/lib/tomcat/webapps -R

COPY container-files /

RUN systemctl enable tomcat_image_init.service \
&& systemctl enable tomcat.service \
&& rm -rf /tmp/bloat

EXPOSE 8080