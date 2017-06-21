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
                 
                        ALTER TABLE rule DROP CONSTRAINT IF EXISTS rule_enum_expression_type;
                        ALTER TABLE rule ADD CONSTRAINT rule_enum_expression_type CHECK (expression_type IN('HOA','PHP'));
                 
                        ALTER TABLE rule_code DROP CONSTRAINT IF EXISTS rule_code_enum_type;
                        ALTER TABLE rule_code ADD CONSTRAINT rule_code_enum_type CHECK (type IN('VARIABLE','EXEC_IF_TRUE','EXEC_IF_FALSE'));
                 
                        ALTER TABLE code_snippet DROP CONSTRAINT IF EXISTS code_snippet_enum_language;
                        ALTER TABLE code_snippet ADD CONSTRAINT code_snippet_enum_language CHECK (language IN('PHP'));
                 
                        ALTER TABLE code_snippet DROP CONSTRAINT IF EXISTS code_snippet_enum_type;
                        ALTER TABLE code_snippet ADD CONSTRAINT code_snippet_enum_type CHECK (type IN('EXPRESSION','FUNCTION_BODY'));
                 
                        ALTER TABLE widget_rule DROP CONSTRAINT IF EXISTS widget_rule_enum_evaluation;
                        ALTER TABLE widget_rule ADD CONSTRAINT widget_rule_enum_evaluation CHECK (evaluation IN('BEFORE_DEFINITION','BEFORE_VALIDATION'));
                 