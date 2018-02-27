#!/bin/bash

sleep 10
sudo -u www-data php /app/app/console cool:database:createSessionTable
