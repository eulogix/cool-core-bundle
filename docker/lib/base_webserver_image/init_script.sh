#!/bin/bash

sleep 20

sudo -u www-data php /app/app/console cool:database:createSessionTable
