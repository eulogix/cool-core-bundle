#!/usr/bin/env bash
mkdir "${PGDATA}"

chown postgres:postgres -R "${PGDATA}"

/usr/pgsql-9.6/bin/postgresql96-setup initdb

sed -i -E "s|^#listen_address.+?|listen_addresses = '*'|" "${PGDATA}/postgresql.conf"
cat /opt/pg_misc/pg_hba.conf >"${PGDATA}/pg_hba.conf"

service postgresql-9.6 start

sudo -u postgres psql -U postgres -d postgres -c "alter user postgres with password 'postgres';"

psql --user=postgres postgres </opt/pg_misc/recreate_dbs.sql