#!/usr/bin/env bash
echo "base php image init script..."

if [ "$PROFILER_ENABLED" = "1" ]; then
    echo "enabling profiler..."
    php_profiler_enable.sh
elif [ "$DEBUGGER_ENABLED" = "1" ]; then
    echo "enabling debugger..."
    php_debugger_enable.sh
else
    echo "disabling debugger..."
    php_debugger_disable.sh
fi