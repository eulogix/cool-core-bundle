#!/usr/bin/env bash
../../bin/wgetcache https://github.com/Activiti/Activiti/releases/download/activiti-5.16.3/activiti-5.16.3.zip
# http://sourceforge.net/projects/php-java-bridge/files/Binary%20package/php-java-bridge_7.2.1/php-java-bridge_7.2.1_documentation.zip/download
../../bin/wgetcache https://ufpr.dl.sourceforge.net/project/php-java-bridge/Binary%20package/php-java-bridge_7.2.1/php-java-bridge_7.2.1_documentation.zip javabridge.zip

docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_tomcat_image .

rm -f *.zip