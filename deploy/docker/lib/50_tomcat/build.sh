#!/usr/bin/env bash
././../../../lib/wget/activiti.sh
cp -R ../../../lib .

docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_tomcat_image .

rm -rf lib
rm -f *.zip