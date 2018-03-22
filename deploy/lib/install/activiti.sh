#!/usr/bin/env bash

# depends on java, maven, tomcat

cd /tmp/bloat
wget https://jdbc.postgresql.org/download/postgresql-42.2.1.jar
unzip activiti-5.16.3.zip
mv activiti-5.16.3/wars/activiti-rest.war /var/lib/tomcat/webapps/
unzip javabridge.zip
mv JavaBridge.war /var/lib/tomcat/webapps/
cd /var/lib/tomcat/webapps
unzip activiti-rest.war -d activiti-rest
unzip -o JavaBridge.war -d JavaBridge
rm -f *.war
cp /tmp/bloat/postgresql-42.2.1.jar activiti-rest/WEB-INF/lib/
cd /tmp/bloat
git clone https://github.com/eulogix/cool-activiti-integration.git
cd cool-activiti-integration
mvn clean install
cp target/uber*.jar /var/lib/tomcat/webapps/activiti-rest/WEB-INF/lib/
cd /tmp
rm -rf bloat