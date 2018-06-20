#!/usr/bin/env bash
rpm -U https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
make_yum_proxy_friendly.sh
yum install -y --nogpgcheck sudo initscripts net-tools wget zip unzip crontabs htop iotop telnet ImageMagick

# wkhtml2pdf
yum install -y xorg-x11-fonts-75dpi
rpm -Uvhi https://downloads.wkhtmltopdf.org/0.12/0.12.5/wkhtmltox-0.12.5-1.centos7.x86_64.rpm
