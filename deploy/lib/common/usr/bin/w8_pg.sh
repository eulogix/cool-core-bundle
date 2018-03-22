#!/bin/bash
while ! /usr/pgsql-9.6/bin/pg_isready -h db > /dev/null 2> /dev/null; do
    sleep 1
done