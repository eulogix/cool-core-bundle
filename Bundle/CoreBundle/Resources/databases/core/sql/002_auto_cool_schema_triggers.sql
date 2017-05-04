SET lc_messages TO 'en_US.UTF-8';

SET SCHEMA 'core';

            CREATE OR REPLACE FUNCTION account_hash_password() 
            RETURNS trigger AS $$
                DECLARE
                        oldpath text;
                    BEGIN
                        oldpath := current_setting('search_path');
                        PERFORM set_config('search_path', TG_TABLE_SCHEMA, true);
                        
            
                    NEW.hashed_password = md5(NEW.password);
                    /*NEW.password=''hidden'';*/
                    PERFORM set_config('search_path', oldpath, false);
RETURN NEW;
                
        
                    END
            $$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS T_account_hash_password ON core.account;

            CREATE TRIGGER T_account_hash_password BEFORE INSERT OR UPDATE ON core.account
              FOR EACH ROW
              EXECUTE PROCEDURE account_hash_password();

            CREATE OR REPLACE FUNCTION table_extension_field_DELETE_FIELDS() 
            RETURNS trigger AS $$
                DECLARE
                        oldpath text;
                    BEGIN
                        oldpath := current_setting('search_path');
                        PERFORM set_config('search_path', TG_TABLE_SCHEMA, true);
                        
            DELETE FROM field_definition WHERE field_definition_id = OLD.field_definition_id;
            PERFORM set_config('search_path', oldpath, false);
RETURN NULL; -- ignored, this is an AFTER trigger
        
                    END
            $$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS T_table_extension_field_DELETE_FIELDS ON core.table_extension_field;

            CREATE TRIGGER T_table_extension_field_DELETE_FIELDS AFTER DELETE ON core.table_extension_field
              FOR EACH ROW
              EXECUTE PROCEDURE table_extension_field_DELETE_FIELDS();

            CREATE OR REPLACE FUNCTION translation_activate() 
            RETURNS trigger AS $$
                DECLARE
                        oldpath text;
                    BEGIN
                        oldpath := current_setting('search_path');
                        PERFORM set_config('search_path', TG_TABLE_SCHEMA, true);
                        
            
                IF ( NOT(NEW.active_flag) OR NEW.active_flag IS NULL)
                AND (NEW.value IS NOT NULL AND NOT(NEW.value ~ E'.*?[[T]]$')) THEN
                        NEW.active_flag := true;
                END IF;
                PERFORM set_config('search_path', oldpath, false);
RETURN NEW;
            
        
                    END
            $$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS T_translation_activate ON core.translation;

            CREATE TRIGGER T_translation_activate BEFORE INSERT OR UPDATE ON core.translation
              FOR EACH ROW
              EXECUTE PROCEDURE translation_activate();

            CREATE OR REPLACE FUNCTION file_property_DELETE_FIELDS() 
            RETURNS trigger AS $$
                DECLARE
                        oldpath text;
                    BEGIN
                        oldpath := current_setting('search_path');
                        PERFORM set_config('search_path', TG_TABLE_SCHEMA, true);
                        
            DELETE FROM field_definition WHERE field_definition_id = OLD.field_definition_id;
            PERFORM set_config('search_path', oldpath, false);
RETURN NULL; -- ignored, this is an AFTER trigger
        
                    END
            $$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS T_file_property_DELETE_FIELDS ON core.file_property;

            CREATE TRIGGER T_file_property_DELETE_FIELDS AFTER DELETE ON core.file_property
              FOR EACH ROW
              EXECUTE PROCEDURE file_property_DELETE_FIELDS();
