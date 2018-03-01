#!/bin/sh

# give rundeck some time to warm up
until [[ $(rd system info) =~ "healthcheck:" ]]; do
  sleep 2
done

output=$(rd projects list | grep -q '0 Projects');
if [ $? -eq 0 ]; then
  rd projects create -p "APP"
  php /usr/bin/build_token.php
  /usr/bin/import_jobs.sh
fi

