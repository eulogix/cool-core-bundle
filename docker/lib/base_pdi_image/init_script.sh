#!/bin/bash

. /etc/profile

sudo -u www-data /opt/data-integration/carte.sh /opt/data-integration/pwd/carte-config.xml >/var/log/pentaho/carte.log 2>/var/log/pentaho/carte.errs