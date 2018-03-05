#!/usr/bin/env bash
# https://sourceforge.net/projects/pentaho/files/Data%20Integration/7.1/pdi-ce-7.1.0.0-12.zip/download -O pdi.zip
../../bin/wgetcache https://iweb.dl.sourceforge.net/project/pentaho/Data%20Integration/7.1/pdi-ce-7.1.0.0-12.zip pdi.zip

docker build --network=host --build-arg http_proxy=http://127.0.0.1:3128 -t cool_base_pdi_image .

rm -f pdi.zip