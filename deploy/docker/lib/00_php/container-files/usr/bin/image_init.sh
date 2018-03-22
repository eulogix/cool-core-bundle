#!/bin/bash

# this script runs some initialization scripts before systemd starts
# so that env vars can be saved and services configured

run-parts /config/init

exec /usr/sbin/init