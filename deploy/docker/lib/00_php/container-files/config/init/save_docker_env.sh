#!/usr/bin/env bash

# this saves all the env vars of docker, which get otherwise lost with systemd containers
touch /etc/profile.d/docker_env.sh


sysdDefault="DefaultEnvironment="
envKeep="Defaults    env_keep += \""
for var in $(compgen -e); do
    echo "export $var=${!var}" >>/etc/profile.d/docker_env.sh
    sysdDefault+="\"$var=${!var}\" "
    envKeep+="$var "
done
envKeep+="\""

# systemd context, for services
echo "$sysdDefault" >>/etc/systemd/system.conf

# sudo -u keeps envs
echo "$envKeep" >/etc/sudoers.d/dockerenv