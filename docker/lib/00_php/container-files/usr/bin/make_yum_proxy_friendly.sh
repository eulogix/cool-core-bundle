#!/bin/bash
# run this in your docker file whenever a new yum repository is added
# this makes it ignore mirrors and aids cache hits with your proxy
rm -f /etc/yum/pluginconf.d/fastestmirror.conf
sed -i -e "s|^mirrorlist=|#mirrorlist=|" /etc/yum.repos.d/*.repo
sed -i -e "s|^#baseurl=|baseurl=|" /etc/yum.repos.d/*.repo