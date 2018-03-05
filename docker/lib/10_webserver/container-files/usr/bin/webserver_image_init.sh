#!/bin/bash
w8_pg.sh
sudo -u www-data php /app/app/console cool:database:createSessionTable
