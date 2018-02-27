<?php

/**
 * creates a token and updates the app config.
 */

$cmdOutput = shell_exec("rd tokens create -u admin -r admin");

if(preg_match('/# API Token created:.*?^(.+?)$/sim', $cmdOutput, $m)) {
  shell_exec("sed -i -E 's/rundeck_auth_token.*/rundeck_auth_token: \"{$m[1]}\"/g'  /app/app/config/parameters.yml");
}