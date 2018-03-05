#!/usr/bin/env bash
echo "base php image init script..."

if [ "$PROFILER_ENABLED" = "1" ]; then
    echo "enabling profiler..."
    /usr/bin/php_profiler_enable.sh
elif [ "$DEBUGGER_ENABLED" = "1" ]; then
    echo "enabling debugger..."
    /usr/bin/php_profiler_disable.sh
else
    echo "disabling debugger..."
    /usr/bin/php_debugger_disable.sh
fi


# init shared folder here, so that no volume is created at build time and
# /shared can hence work with host mappings in compose.yml for derived images

mkdir /shared/xdebug_output || true
mkdir /shared/tmp || true
mkdir /shared/log || true
chmod 777 /shared/xdebug_output
chmod 777 /shared/tmp
chmod 777 /shared/log