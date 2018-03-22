#!/usr/bin/env bash

echo "root:root" | chpasswd

useradd www-data
groupmod -g 33 www-data
usermod -u 33 www-data
