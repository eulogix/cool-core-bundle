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