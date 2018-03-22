#!/usr/bin/env bash
# pdi.zip is assumed to be in /opt

cd /opt
unzip pdi.zip
rm -f pdi.zip
git clone https://github.com/eulogix/cool-kettle-integration.git
git clone https://github.com/eulogix/kettle-plugins.git
recompile_plugins.sh
sudo -u root rm -rf /opt/data-integration/plugins/plugins_compiled
sudo -u root cp -R /opt/plugins_compiled /opt/data-integration/plugins/
sudo -u root chown www-data:www-data -R /opt/data-integration