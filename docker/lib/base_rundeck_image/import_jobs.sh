#!/bin/bash
cd /app
sudo -u www-data php app/console cool:rundeck:exportjobs APP cool
