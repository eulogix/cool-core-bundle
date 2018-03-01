#!/bin/bash
if [ "$( psql -h db -U postgres -tAc "SELECT 1 FROM pg_database WHERE datname='activiti'" )" = '1' ]
then
    echo "Database already exists"
else
  cd /tmp
  echo "CREATE DATABASE activiti;" >createdb.sql
  psql -h db -U postgres <createdb.sql
  wget https://raw.githubusercontent.com/Activiti/Activiti/activiti-5.16.3/modules/activiti-engine/src/main/resources/org/activiti/db/create/activiti.postgres.create.engine.sql
  psql -h db -U postgres --dbname=activiti <activiti.postgres.create.engine.sql
  wget https://raw.githubusercontent.com/Activiti/Activiti/activiti-5.16.3/modules/activiti-engine/src/main/resources/org/activiti/db/create/activiti.postgres.create.history.sql
  psql -h db -U postgres --dbname=activiti <activiti.postgres.create.history.sql
  wget https://raw.githubusercontent.com/Activiti/Activiti/activiti-5.16.3/modules/activiti-engine/src/main/resources/org/activiti/db/create/activiti.postgres.create.identity.sql
  psql -h db -U postgres --dbname=activiti <activiti.postgres.create.identity.sql
fi

service tomcat start