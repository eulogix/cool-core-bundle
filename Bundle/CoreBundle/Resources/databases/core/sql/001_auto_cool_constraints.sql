SET lc_messages TO 'en_US.UTF-8';

SET SCHEMA 'core';

        CREATE OR REPLACE FUNCTION check_constraints_field_definition()
        RETURNS trigger AS $$
 if(!NEW.name.match(/^[a-z0-9_]+$/)) {
                       plv8.elog(ERROR, 'error', 'name', 'NO_SPECIAL_CHARACTERS');
                    }
 return NEW;
        $$ LANGUAGE plv8;

        DROP TRIGGER IF EXISTS T_check_constraints_field_definition ON core.field_definition;

        CREATE TRIGGER T_check_constraints_field_definition BEFORE INSERT OR UPDATE ON core.field_definition
          FOR EACH ROW
          EXECUTE PROCEDURE check_constraints_field_definition();
