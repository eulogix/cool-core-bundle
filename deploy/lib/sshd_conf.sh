#!/usr/bin/env bash
sed -i -E 's/^#PermitRootLogin yes/PermitRootLogin yes/'  /etc/ssh/sshd_config
sed -i -E 's/^#PasswordAuthentication yes/PasswordAuthentication yes/'  /etc/ssh/sshd_config
sed -i -E 's/^PasswordAuthentication no/#PasswordAuthentication no/'  /etc/ssh/sshd_config
sed -i -E 's/^GatewayPorts no/GatewayPorts yes/'  /etc/ssh/sshd_config
