SET lc_messages TO 'en_US.UTF-8';


     ALTER TABLE core.account DROP CONSTRAINT IF EXISTS core_account_type_FK;
     ALTER TABLE core.account ADD CONSTRAINT core_account_type_FK
                            FOREIGN KEY (type)
                            REFERENCES lookups.core_user_type (value)
                            ON DELETE RESTRICT
                            ON UPDATE CASCADE;
                            
     ALTER TABLE core.account DROP CONSTRAINT IF EXISTS core_account_office_FK;
     ALTER TABLE core.account ADD CONSTRAINT core_account_office_FK
                            FOREIGN KEY (office)
                            REFERENCES lookups.core_account_office (value)
                            ON DELETE RESTRICT
                            ON UPDATE CASCADE;
                            
     ALTER TABLE core.app_setting DROP CONSTRAINT IF EXISTS core_app_setting_name_FK;
     ALTER TABLE core.app_setting ADD CONSTRAINT core_app_setting_name_FK
                            FOREIGN KEY (name)
                            REFERENCES lookups.app_setting_name (value)
                            ON DELETE RESTRICT
                            ON UPDATE CASCADE;
                            
     ALTER TABLE core.app_setting DROP CONSTRAINT IF EXISTS core_app_setting_space_FK;
     ALTER TABLE core.app_setting ADD CONSTRAINT core_app_setting_space_FK
                            FOREIGN KEY (space)
                            REFERENCES lookups.app_setting_space (value)
                            ON DELETE RESTRICT
                            ON UPDATE CASCADE;
                            
     ALTER TABLE core.account_group DROP CONSTRAINT IF EXISTS core_account_group_type_FK;
     ALTER TABLE core.account_group ADD CONSTRAINT core_account_group_type_FK
                            FOREIGN KEY (type)
                            REFERENCES lookups.core_group_type (value)
                            ON DELETE RESTRICT
                            ON UPDATE CASCADE;
                            
     ALTER TABLE core.user_reminder DROP CONSTRAINT IF EXISTS core_user_reminder_category_FK;
     ALTER TABLE core.user_reminder ADD CONSTRAINT core_user_reminder_category_FK
                            FOREIGN KEY (category)
                            REFERENCES lookups.core_user_reminder_category (value)
                            ON DELETE RESTRICT
                            ON UPDATE CASCADE;
                            
     ALTER TABLE core.rule DROP CONSTRAINT IF EXISTS core_rule_category_FK;
     ALTER TABLE core.rule ADD CONSTRAINT core_rule_category_FK
                            FOREIGN KEY (category)
                            REFERENCES lookups.rule_category (value)
                            ON DELETE RESTRICT
                            ON UPDATE CASCADE;
                            
     ALTER TABLE core.code_snippet DROP CONSTRAINT IF EXISTS core_code_snippet_category_FK;
     ALTER TABLE core.code_snippet ADD CONSTRAINT core_code_snippet_category_FK
                            FOREIGN KEY (category)
                            REFERENCES lookups.code_snippet_category (value)
                            ON DELETE RESTRICT
                            ON UPDATE CASCADE;
                            