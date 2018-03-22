#!/usr/bin/env bash
chmod +x bin/*
startDir="$PWD"
for d in lib/*/ ; do
    cd "./$d"
    ./build.sh
    cd "$startDir"
done

