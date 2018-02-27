#!/usr/bin/env bash
cd lib/base_php_image
docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_php_image .
cd ..

cd base_webserver_image
docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_webserver_image .
cd ..

cd base_pg_image
docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_pg_image .
cd ..

cd base_rundeck_image
docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_rundeck_image .
cd ..



