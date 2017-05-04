SET lc_messages TO 'en_US.UTF-8';

CREATE TABLE IF NOT EXISTS lookups.core_user_type
                    (value TEXT,
dec_en TEXT,
dec_es TEXT,
dec_pt TEXT,
dec_it TEXT,
sort_order INTEGER,
mandatory_flag BOOLEAN,
filter TEXT[],
schema_filter TEXT[],
original_value TEXT,
notes TEXT,
PRIMARY KEY (value)
                    );

DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN dec_en TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN dec_es TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN dec_pt TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN dec_it TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN sort_order INTEGER; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN mandatory_flag BOOLEAN; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN schema_filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN original_value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN notes TEXT; EXCEPTION WHEN OTHERS THEN END; $$;

     ALTER TABLE core.account DROP CONSTRAINT IF EXISTS core_account_type_FK;
     ALTER TABLE core.account ADD CONSTRAINT core_account_type_FK
                            FOREIGN KEY (type)
                            REFERENCES lookups.core_user_type (value)
                            ON DELETE RESTRICT
                            ON UPDATE CASCADE;
                            CREATE TABLE IF NOT EXISTS lookups.app_setting_name
                    (value TEXT,
dec_en TEXT,
dec_es TEXT,
dec_pt TEXT,
dec_it TEXT,
sort_order INTEGER,
mandatory_flag BOOLEAN,
filter TEXT[],
schema_filter TEXT[],
original_value TEXT,
notes TEXT,
PRIMARY KEY (value)
                    );

DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN dec_en TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN dec_es TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN dec_pt TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN dec_it TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN sort_order INTEGER; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN mandatory_flag BOOLEAN; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN schema_filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN original_value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN notes TEXT; EXCEPTION WHEN OTHERS THEN END; $$;

     ALTER TABLE core.app_setting DROP CONSTRAINT IF EXISTS core_app_setting_name_FK;
     ALTER TABLE core.app_setting ADD CONSTRAINT core_app_setting_name_FK
                            FOREIGN KEY (name)
                            REFERENCES lookups.app_setting_name (value)
                            ON DELETE RESTRICT
                            ON UPDATE CASCADE;
                            CREATE TABLE IF NOT EXISTS lookups.app_setting_space
                    (value TEXT,
dec_en TEXT,
dec_es TEXT,
dec_pt TEXT,
dec_it TEXT,
sort_order INTEGER,
mandatory_flag BOOLEAN,
filter TEXT[],
schema_filter TEXT[],
original_value TEXT,
notes TEXT,
PRIMARY KEY (value)
                    );

DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN dec_en TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN dec_es TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN dec_pt TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN dec_it TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN sort_order INTEGER; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN mandatory_flag BOOLEAN; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN schema_filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN original_value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN notes TEXT; EXCEPTION WHEN OTHERS THEN END; $$;

     ALTER TABLE core.app_setting DROP CONSTRAINT IF EXISTS core_app_setting_space_FK;
     ALTER TABLE core.app_setting ADD CONSTRAINT core_app_setting_space_FK
                            FOREIGN KEY (space)
                            REFERENCES lookups.app_setting_space (value)
                            ON DELETE RESTRICT
                            ON UPDATE CASCADE;
                            CREATE TABLE IF NOT EXISTS lookups.core_group_type
                    (value TEXT,
dec_en TEXT,
dec_es TEXT,
dec_pt TEXT,
dec_it TEXT,
sort_order INTEGER,
mandatory_flag BOOLEAN,
filter TEXT[],
schema_filter TEXT[],
original_value TEXT,
notes TEXT,
PRIMARY KEY (value)
                    );

DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN dec_en TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN dec_es TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN dec_pt TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN dec_it TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN sort_order INTEGER; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN mandatory_flag BOOLEAN; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN schema_filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN original_value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN notes TEXT; EXCEPTION WHEN OTHERS THEN END; $$;

     ALTER TABLE core.account_group DROP CONSTRAINT IF EXISTS core_account_group_type_FK;
     ALTER TABLE core.account_group ADD CONSTRAINT core_account_group_type_FK
                            FOREIGN KEY (type)
                            REFERENCES lookups.core_group_type (value)
                            ON DELETE RESTRICT
                            ON UPDATE CASCADE;
                            