#!/usr/bin/env bash

# this saves all the env vars of docker, which get otherwise lost with systemd containers
touch /etc/profile.d/docker_env.sh

sysdDefault="DefaultEnvironment="
for var in $(compgen -e); do
    echo "export $var=${!var}" >>/etc/profile.d/docker_env.sh
    sysdDefault+="\"$var=${!var}\" "
done
echo "$sysdDefault" >>/etc/systemd/system.conf