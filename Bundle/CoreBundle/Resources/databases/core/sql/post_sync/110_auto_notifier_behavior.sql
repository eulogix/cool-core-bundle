/* file generation UUID: 59f0c343049cf */

--
-- Notifier triggers for account
--

CREATE OR REPLACE FUNCTION account_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_account', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_account',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER account_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON account
    FOR EACH STATEMENT EXECUTE PROCEDURE account_notf();



--
-- Notifier triggers for app_setting
--

CREATE OR REPLACE FUNCTION app_setting_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_app_setting', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_app_setting',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER app_setting_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON app_setting
    FOR EACH STATEMENT EXECUTE PROCEDURE app_setting_notf();



--
-- Notifier triggers for account_setting
--

CREATE OR REPLACE FUNCTION account_setting_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_account_setting', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_account_setting',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER account_setting_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON account_setting
    FOR EACH STATEMENT EXECUTE PROCEDURE account_setting_notf();



--
-- Notifier triggers for account_profile
--

CREATE OR REPLACE FUNCTION account_profile_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_account_profile', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_account_profile',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER account_profile_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON account_profile
    FOR EACH STATEMENT EXECUTE PROCEDURE account_profile_notf();



--
-- Notifier triggers for account_profile_setting
--

CREATE OR REPLACE FUNCTION account_profile_setting_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_account_profile_setting', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_account_profile_setting',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER account_profile_setting_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON account_profile_setting
    FOR EACH STATEMENT EXECUTE PROCEDURE account_profile_setting_notf();



--
-- Notifier triggers for account_profile_ref
--

CREATE OR REPLACE FUNCTION account_profile_ref_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_account_profile_ref', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_account_profile_ref',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER account_profile_ref_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON account_profile_ref
    FOR EACH STATEMENT EXECUTE PROCEDURE account_profile_ref_notf();



--
-- Notifier triggers for account_group
--

CREATE OR REPLACE FUNCTION account_group_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_account_group', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_account_group',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER account_group_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON account_group
    FOR EACH STATEMENT EXECUTE PROCEDURE account_group_notf();



--
-- Notifier triggers for account_group_ref
--

CREATE OR REPLACE FUNCTION account_group_ref_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_account_group_ref', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_account_group_ref',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER account_group_ref_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON account_group_ref
    FOR EACH STATEMENT EXECUTE PROCEDURE account_group_ref_notf();



--
-- Notifier triggers for form_config
--

CREATE OR REPLACE FUNCTION form_config_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_form_config', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_form_config',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER form_config_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON form_config
    FOR EACH STATEMENT EXECUTE PROCEDURE form_config_notf();



--
-- Notifier triggers for form_config_field
--

CREATE OR REPLACE FUNCTION form_config_field_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_form_config_field', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_form_config_field',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER form_config_field_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON form_config_field
    FOR EACH STATEMENT EXECUTE PROCEDURE form_config_field_notf();



--
-- Notifier triggers for lister_config
--

CREATE OR REPLACE FUNCTION lister_config_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_lister_config', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_lister_config',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER lister_config_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON lister_config
    FOR EACH STATEMENT EXECUTE PROCEDURE lister_config_notf();



--
-- Notifier triggers for lister_config_column
--

CREATE OR REPLACE FUNCTION lister_config_column_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_lister_config_column', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_lister_config_column',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER lister_config_column_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON lister_config_column
    FOR EACH STATEMENT EXECUTE PROCEDURE lister_config_column_notf();



--
-- Notifier triggers for lookup
--

CREATE OR REPLACE FUNCTION lookup_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_lookup', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_lookup',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER lookup_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON lookup
    FOR EACH STATEMENT EXECUTE PROCEDURE lookup_notf();



--
-- Notifier triggers for table_extension
--

CREATE OR REPLACE FUNCTION table_extension_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_table_extension', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_table_extension',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER table_extension_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON table_extension
    FOR EACH STATEMENT EXECUTE PROCEDURE table_extension_notf();



--
-- Notifier triggers for table_extension_field
--

CREATE OR REPLACE FUNCTION table_extension_field_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_table_extension_field', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_table_extension_field',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER table_extension_field_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON table_extension_field
    FOR EACH STATEMENT EXECUTE PROCEDURE table_extension_field_notf();



--
-- Notifier triggers for field_definition
--

CREATE OR REPLACE FUNCTION field_definition_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_field_definition', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_field_definition',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER field_definition_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON field_definition
    FOR EACH STATEMENT EXECUTE PROCEDURE field_definition_notf();



--
-- Notifier triggers for app_lock
--

CREATE OR REPLACE FUNCTION app_lock_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_app_lock', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_app_lock',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER app_lock_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON app_lock
    FOR EACH STATEMENT EXECUTE PROCEDURE app_lock_notf();



--
-- Notifier triggers for translation
--

CREATE OR REPLACE FUNCTION translation_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_translation', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_translation',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER translation_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON translation
    FOR EACH STATEMENT EXECUTE PROCEDURE translation_notf();



--
-- Notifier triggers for pending_call
--

CREATE OR REPLACE FUNCTION pending_call_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_pending_call', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_pending_call',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER pending_call_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON pending_call
    FOR EACH STATEMENT EXECUTE PROCEDURE pending_call_notf();



--
-- Notifier triggers for async_job
--

CREATE OR REPLACE FUNCTION async_job_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_async_job', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_async_job',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER async_job_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON async_job
    FOR EACH STATEMENT EXECUTE PROCEDURE async_job_notf();



--
-- Notifier triggers for user_notification
--

CREATE OR REPLACE FUNCTION user_notification_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_user_notification', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_user_notification',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER user_notification_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON user_notification
    FOR EACH STATEMENT EXECUTE PROCEDURE user_notification_notf();



--
-- Notifier triggers for file_property
--

CREATE OR REPLACE FUNCTION file_property_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_file_property', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_file_property',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER file_property_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON file_property
    FOR EACH STATEMENT EXECUTE PROCEDURE file_property_notf();



--
-- Notifier triggers for user_reminder
--

CREATE OR REPLACE FUNCTION user_reminder_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_user_reminder', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_user_reminder',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER user_reminder_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON user_reminder
    FOR EACH STATEMENT EXECUTE PROCEDURE user_reminder_notf();



--
-- Notifier triggers for rule
--

CREATE OR REPLACE FUNCTION rule_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_rule', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_rule',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER rule_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON rule
    FOR EACH STATEMENT EXECUTE PROCEDURE rule_notf();



--
-- Notifier triggers for rule_code
--

CREATE OR REPLACE FUNCTION rule_code_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_rule_code', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_rule_code',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER rule_code_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON rule_code
    FOR EACH STATEMENT EXECUTE PROCEDURE rule_code_notf();



--
-- Notifier triggers for code_snippet
--

CREATE OR REPLACE FUNCTION code_snippet_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_code_snippet', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_code_snippet',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER code_snippet_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON code_snippet
    FOR EACH STATEMENT EXECUTE PROCEDURE code_snippet_notf();



--
-- Notifier triggers for code_snippet_variable
--

CREATE OR REPLACE FUNCTION code_snippet_variable_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_code_snippet_variable', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_code_snippet_variable',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER code_snippet_variable_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON code_snippet_variable
    FOR EACH STATEMENT EXECUTE PROCEDURE code_snippet_variable_notf();



--
-- Notifier triggers for widget_rule
--

CREATE OR REPLACE FUNCTION widget_rule_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_widget_rule', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_widget_rule',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER widget_rule_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON widget_rule
    FOR EACH STATEMENT EXECUTE PROCEDURE widget_rule_notf();



--
-- Notifier triggers for pg_listener_hook
--

CREATE OR REPLACE FUNCTION pg_listener_hook_notf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        PERFORM pg_notify('c_pg_listener_hook', '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || 'c_pg_listener_hook',  '{ "schema": "core", "actual_schema": "' || TG_TABLE_SCHEMA || '", "operation": "' || TG_OP || '" }');
        RETURN NULL;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER pg_listener_hook_notf_notf_trg AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON pg_listener_hook
    FOR EACH STATEMENT EXECUTE PROCEDURE pg_listener_hook_notf();


