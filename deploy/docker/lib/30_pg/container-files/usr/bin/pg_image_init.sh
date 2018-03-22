#!/bin/bash
if [ ! -f "${PGDATA}/postgresql.conf" ]; then

  ./usr/bin/initialize_pg.sh

  ./usr/bin/custom_init_script.sh

else
  service postgresql-9.6 start
fi
