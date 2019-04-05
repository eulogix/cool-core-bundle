SET lc_messages TO 'en_US.UTF-8';

CREATE TABLE IF NOT EXISTS lookups.core_user_type
                    (value TEXT,
dec_en TEXT,
dec_es TEXT,
dec_pt TEXT,
dec_it TEXT,
dec_el TEXT,
sort_order INTEGER,
mandatory_flag BOOLEAN,
filter TEXT[],
schema_filter TEXT[],
schema_filter_inv TEXT[],
original_value TEXT,
notes TEXT,
PRIMARY KEY (value)
                    );

DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN dec_en TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN dec_es TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN dec_pt TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN dec_it TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN dec_el TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN sort_order INTEGER; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN mandatory_flag BOOLEAN; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN schema_filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN schema_filter_inv TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN original_value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_type ADD COLUMN notes TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
CREATE TABLE IF NOT EXISTS lookups.app_setting_name
                    (value TEXT,
dec_en TEXT,
dec_es TEXT,
dec_pt TEXT,
dec_it TEXT,
dec_el TEXT,
sort_order INTEGER,
mandatory_flag BOOLEAN,
filter TEXT[],
schema_filter TEXT[],
schema_filter_inv TEXT[],
original_value TEXT,
notes TEXT,
PRIMARY KEY (value)
                    );

DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN dec_en TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN dec_es TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN dec_pt TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN dec_it TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN dec_el TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN sort_order INTEGER; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN mandatory_flag BOOLEAN; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN schema_filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN schema_filter_inv TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN original_value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_name ADD COLUMN notes TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
CREATE TABLE IF NOT EXISTS lookups.app_setting_space
                    (value TEXT,
dec_en TEXT,
dec_es TEXT,
dec_pt TEXT,
dec_it TEXT,
dec_el TEXT,
sort_order INTEGER,
mandatory_flag BOOLEAN,
filter TEXT[],
schema_filter TEXT[],
schema_filter_inv TEXT[],
original_value TEXT,
notes TEXT,
PRIMARY KEY (value)
                    );

DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN dec_en TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN dec_es TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN dec_pt TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN dec_it TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN dec_el TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN sort_order INTEGER; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN mandatory_flag BOOLEAN; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN schema_filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN schema_filter_inv TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN original_value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.app_setting_space ADD COLUMN notes TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
CREATE TABLE IF NOT EXISTS lookups.core_group_type
                    (value TEXT,
dec_en TEXT,
dec_es TEXT,
dec_pt TEXT,
dec_it TEXT,
dec_el TEXT,
sort_order INTEGER,
mandatory_flag BOOLEAN,
filter TEXT[],
schema_filter TEXT[],
schema_filter_inv TEXT[],
original_value TEXT,
notes TEXT,
PRIMARY KEY (value)
                    );

DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN dec_en TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN dec_es TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN dec_pt TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN dec_it TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN dec_el TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN sort_order INTEGER; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN mandatory_flag BOOLEAN; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN schema_filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN schema_filter_inv TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN original_value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_group_type ADD COLUMN notes TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
CREATE TABLE IF NOT EXISTS lookups.core_user_reminder_category
                    (value TEXT,
dec_en TEXT,
dec_es TEXT,
dec_pt TEXT,
dec_it TEXT,
dec_el TEXT,
sort_order INTEGER,
mandatory_flag BOOLEAN,
filter TEXT[],
schema_filter TEXT[],
schema_filter_inv TEXT[],
original_value TEXT,
notes TEXT,
PRIMARY KEY (value)
                    );

DO $$ BEGIN ALTER TABLE lookups.core_user_reminder_category ADD COLUMN value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_reminder_category ADD COLUMN dec_en TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_reminder_category ADD COLUMN dec_es TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_reminder_category ADD COLUMN dec_pt TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_reminder_category ADD COLUMN dec_it TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_reminder_category ADD COLUMN dec_el TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_reminder_category ADD COLUMN sort_order INTEGER; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_reminder_category ADD COLUMN mandatory_flag BOOLEAN; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_reminder_category ADD COLUMN filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_reminder_category ADD COLUMN schema_filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_reminder_category ADD COLUMN schema_filter_inv TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_reminder_category ADD COLUMN original_value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.core_user_reminder_category ADD COLUMN notes TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
CREATE TABLE IF NOT EXISTS lookups.rule_category
                    (value TEXT,
dec_en TEXT,
dec_es TEXT,
dec_pt TEXT,
dec_it TEXT,
dec_el TEXT,
sort_order INTEGER,
mandatory_flag BOOLEAN,
filter TEXT[],
schema_filter TEXT[],
schema_filter_inv TEXT[],
original_value TEXT,
notes TEXT,
PRIMARY KEY (value)
                    );

DO $$ BEGIN ALTER TABLE lookups.rule_category ADD COLUMN value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.rule_category ADD COLUMN dec_en TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.rule_category ADD COLUMN dec_es TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.rule_category ADD COLUMN dec_pt TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.rule_category ADD COLUMN dec_it TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.rule_category ADD COLUMN dec_el TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.rule_category ADD COLUMN sort_order INTEGER; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.rule_category ADD COLUMN mandatory_flag BOOLEAN; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.rule_category ADD COLUMN filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.rule_category ADD COLUMN schema_filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.rule_category ADD COLUMN schema_filter_inv TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.rule_category ADD COLUMN original_value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.rule_category ADD COLUMN notes TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
CREATE TABLE IF NOT EXISTS lookups.code_snippet_category
                    (value TEXT,
dec_en TEXT,
dec_es TEXT,
dec_pt TEXT,
dec_it TEXT,
dec_el TEXT,
sort_order INTEGER,
mandatory_flag BOOLEAN,
filter TEXT[],
schema_filter TEXT[],
schema_filter_inv TEXT[],
original_value TEXT,
notes TEXT,
PRIMARY KEY (value)
                    );

DO $$ BEGIN ALTER TABLE lookups.code_snippet_category ADD COLUMN value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.code_snippet_category ADD COLUMN dec_en TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.code_snippet_category ADD COLUMN dec_es TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.code_snippet_category ADD COLUMN dec_pt TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.code_snippet_category ADD COLUMN dec_it TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.code_snippet_category ADD COLUMN dec_el TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.code_snippet_category ADD COLUMN sort_order INTEGER; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.code_snippet_category ADD COLUMN mandatory_flag BOOLEAN; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.code_snippet_category ADD COLUMN filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.code_snippet_category ADD COLUMN schema_filter TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.code_snippet_category ADD COLUMN schema_filter_inv TEXT[]; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.code_snippet_category ADD COLUMN original_value TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
DO $$ BEGIN ALTER TABLE lookups.code_snippet_category ADD COLUMN notes TEXT; EXCEPTION WHEN OTHERS THEN END; $$;
