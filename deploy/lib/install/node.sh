#!/usr/bin/env bash
curl -sL https://rpm.nodesource.com/setup_9.x | sudo -E bash -
yum -y install nodejs
npm install -g bower
