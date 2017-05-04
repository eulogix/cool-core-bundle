SET lc_messages TO 'en_US.UTF-8';


                        ALTER TABLE account DROP CONSTRAINT IF EXISTS account_enum_sex;
                        ALTER TABLE account ADD CONSTRAINT account_enum_sex CHECK (sex IN('M','F'));
                 
                        ALTER TABLE lister_config_column DROP CONSTRAINT IF EXISTS lister_config_column_enum_sortby_direction;
                        ALTER TABLE lister_config_column ADD CONSTRAINT lister_config_column_enum_sortby_direction CHECK (sortby_direction IN('ASC','DESC'));
                 
                        ALTER TABLE field_definition DROP CONSTRAINT IF EXISTS field_definition_enum_type;
                        ALTER TABLE field_definition ADD CONSTRAINT field_definition_enum_type CHECK (type IN('STRING','NUMBER','BOOLEAN'));
                 
                        ALTER TABLE field_definition DROP CONSTRAINT IF EXISTS field_definition_enum_control_type;
                        ALTER TABLE field_definition ADD CONSTRAINT field_definition_enum_control_type CHECK (control_type IN('DATE','DATETIME','SELECT','XHRPICKER','INTEGER','DOUBLE','CURRENCY','TEXTBOX','TEXTAREA','CHECKBOX'));
                 
                        ALTER TABLE field_definition DROP CONSTRAINT IF EXISTS field_definition_enum_lookup_type;
                        ALTER TABLE field_definition ADD CONSTRAINT field_definition_enum_lookup_type CHECK (lookup_type IN('OTLT','table','enum','FK','valueMap','valueMapService'));
                 
                        ALTER TABLE async_job DROP CONSTRAINT IF EXISTS async_job_enum_executor_type;
                        ALTER TABLE async_job ADD CONSTRAINT async_job_enum_executor_type CHECK (executor_type IN('rundeck'));
                 
                        ALTER TABLE async_job DROP CONSTRAINT IF EXISTS async_job_enum_outcome;
                        ALTER TABLE async_job ADD CONSTRAINT async_job_enum_outcome CHECK (outcome IN('success','failure'));
                 
                        ALTER TABLE user_reminder DROP CONSTRAINT IF EXISTS user_reminder_enum_type;
                        ALTER TABLE user_reminder ADD CONSTRAINT user_reminder_enum_type CHECK (type IN('SIMPLE','DATED'));
                 