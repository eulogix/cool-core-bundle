<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core;

class BaseDictionary extends \Eulogix\Cool\Lib\Dictionary\Dictionary {

    /*
    Don't modify this class, use the overridable descendant instead    
    */

    public function getSettings() {
        return array (
  'tables' => 
  array (
    'core.account' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'account',
        'editable' => true,
        'defaultLister' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\CWidget\\Core\\User\\UserLister',
        'defaultEditor' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\CWidget\\Core\\User\\UserEditorForm',
        'valueMapDecodingSQL' => 'COALESCE(first_name,\'\') || COALESCE(\' \' || last_name, \'\')',
      ),
      'trigger' => 
      array (
        0 => 
        array (
          'name' => 'password',
          'language' => 'plpgsql',
          'when' => 'BEFORE UPDATE',
          'body' => '
            
                    /*NEW.password=\'\'hidden\'\';*/
                    IF(NEW.hashed_password IS NOT NULL AND  NEW.hashed_password != COALESCE(OLD.hashed_password,\'0\')) THEN
                        NEW.last_password_update = NOW();
                    END IF;
                    return NEW;
                
        ',
        ),
      ),
      'files' => 
      array (
        'category' => 
        array (
          0 => 
          array (
            'name' => 'AVATAR',
            'maxCount' => '1',
            'extensions' => 'jpg',
          ),
          1 => 
          array (
            'name' => 'UNCATEGORIZED',
            'maxSizeMb' => '1',
            'default' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
      ),
      'columns' => 
      array (
        'account_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'login_name' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'hashed_password' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'type' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
          'lookup' => 
          array (
            'type' => 'table',
            'domainName' => 'CORE_USER_TYPE',
          ),
        ),
        'first_name' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'last_name' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'sex' => 
        array (
          'attributes' => 
          array (
          ),
          'lookup' => 
          array (
            'type' => 'enum',
            'validValues' => 'M,F',
          ),
        ),
        'email' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'telephone' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'mobile' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'default_locale' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'company_name' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'validity' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'daterange',
          ),
        ),
        'roles' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'last_password_update' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'validate_method' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
          'lookup' => 
          array (
            'type' => 'enum',
            'validValues' => 'LDAP,LOCAL',
          ),
        ),
        'office' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
          'lookup' => 
          array (
            'type' => 'table',
            'domainName' => 'CORE_ACCOUNT_OFFICE',
          ),
        ),
      ),
    ),
    'core.app_setting' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'app_setting',
        'editable' => true,
      ),
      'columns' => 
      array (
        'app_setting_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'name' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
          'lookup' => 
          array (
            'type' => 'table',
            'domainName' => 'APP_SETTING_NAME',
          ),
        ),
        'space' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
          'lookup' => 
          array (
            'type' => 'table',
            'domainName' => 'APP_SETTING_SPACE',
          ),
        ),
        'value' => 
        array (
          'attributes' => 
          array (
          ),
        ),
      ),
    ),
    'core.account_setting' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'account_setting',
      ),
      'columns' => 
      array (
        'account_setting_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'account_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'name' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'value' => 
        array (
          'attributes' => 
          array (
          ),
        ),
      ),
    ),
    'core.account_profile' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'account_profile',
        'editable' => true,
      ),
      'columns' => 
      array (
        'account_profile_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'name' => 
        array (
          'attributes' => 
          array (
          ),
        ),
      ),
    ),
    'core.account_profile_setting' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'account_profile_setting',
      ),
      'columns' => 
      array (
        'account_profile_setting_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'account_profile_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'name' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'value' => 
        array (
          'attributes' => 
          array (
          ),
        ),
      ),
    ),
    'core.account_profile_ref' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'account_profile_ref',
      ),
      'columns' => 
      array (
        'account_profile_ref_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'account_id' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'xhrpicker',
          ),
        ),
        'account_profile_id' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
        ),
        'sort_order' => 
        array (
          'attributes' => 
          array (
          ),
        ),
      ),
    ),
    'core.account_group' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'account_group',
        'editable' => true,
        'valueMapDecodingSQL' => 'account_group_id::text || \' - \' || name',
      ),
      'columns' => 
      array (
        'account_group_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'type' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
          'lookup' => 
          array (
            'type' => 'table',
            'domainName' => 'CORE_GROUP_TYPE',
          ),
        ),
        'name' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'description' => 
        array (
          'attributes' => 
          array (
          ),
        ),
      ),
    ),
    'core.account_group_ref' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'account_group_ref',
      ),
      'columns' => 
      array (
        'account_group_ref_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'account_id' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'xhrpicker',
          ),
        ),
        'account_group_id' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'xhrpicker',
          ),
        ),
        'role' => 
        array (
          'attributes' => 
          array (
          ),
        ),
      ),
    ),
    'core.form_config' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'form_config',
      ),
      'columns' => 
      array (
        'form_config_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'name' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'variation' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'layout' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'textarea',
          ),
          'lister_control' => 
          array (
            'type' => 'textbox',
          ),
        ),
        'wiki_help_page' => 
        array (
          'attributes' => 
          array (
          ),
        ),
      ),
    ),
    'core.form_config_field' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'form_config_field',
      ),
      'columns' => 
      array (
        'form_config_field_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'form_config_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'name' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'read_only_flag' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'hidden_flag' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'width' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'height' => 
        array (
          'attributes' => 
          array (
          ),
        ),
      ),
    ),
    'core.lister_config' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'lister_config',
      ),
      'columns' => 
      array (
        'lister_config_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'name' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'variation' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'filter_show_flag' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'filter_server_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'min_height' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'max_height' => 
        array (
          'attributes' => 
          array (
          ),
        ),
      ),
    ),
    'core.lister_config_column' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'lister_config_column',
      ),
      'columns' => 
      array (
        'lister_config_column_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'lister_config_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'name' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'sortable_flag' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'editable_flag' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'hidden_flag' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'show_summary_flag' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'width' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'cell_template' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'textarea',
          ),
        ),
        'cell_template_js' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'textarea',
          ),
        ),
        'dijit_widget_template' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'textarea',
          ),
        ),
        'dijit_widget_set_value_js' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'textarea',
          ),
        ),
        'column_style_css' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'sort_order' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'sortby_order' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'sortby_direction' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
          'lookup' => 
          array (
            'type' => 'enum',
            'validValues' => 'ASC,DESC',
          ),
        ),
        'truncate_chars' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'tooltip_js_expression' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'tooltip_url_js_expression' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'tooltip_max_width' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'tooltip_delay_msec' => 
        array (
          'attributes' => 
          array (
          ),
        ),
      ),
    ),
    'core.lookup' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'lookup',
        'editable' => true,
        'defaultLister' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\CWidget\\Core\\Lookup\\LookupLister',
      ),
      'columns' => 
      array (
        'lookup_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'domain_name' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'value' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'dec_it' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'dec_en' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'dec_es' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'dec_pt' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'dec_el' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'sort_order' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'schema_filter' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'filter' => 
        array (
          'attributes' => 
          array (
          ),
        ),
      ),
    ),
    'core.table_extension' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'table_extension',
        'editable' => true,
        'defaultEditor' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\CWidget\\Core\\TableExtension\\TableExtensionEditorForm',
      ),
      'columns' => 
      array (
        'table_extension_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'db_schema' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'db_table' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'active_flag' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'description' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'textarea',
          ),
        ),
      ),
    ),
    'core.table_extension_field' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'table_extension_field',
        'defaultLister' => 'Eulogix/Cool/Bundle/CoreBundle/CWidget/Core/TableExtension/TableExtensionFieldLister',
      ),
      'trigger' => 
      array (
        0 => 
        array (
          'name' => 'DELETE_FIELDS',
          'language' => 'plpgsql',
          'when' => 'AFTER DELETE',
          'body' => '
            DELETE FROM field_definition WHERE field_definition_id = OLD.field_definition_id;
            RETURN NULL; -- ignored, this is an AFTER trigger
        ',
        ),
      ),
      'columns' => 
      array (
        'table_extension_field_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'table_extension_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'field_definition_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'require_index' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'number',
          ),
        ),
        'active_flag' => 
        array (
          'attributes' => 
          array (
          ),
        ),
      ),
    ),
    'core.field_definition' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'field_definition',
      ),
      'columns' => 
      array (
        'field_definition_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'name' => 
        array (
          'attributes' => 
          array (
          ),
          'constraint' => 
          array (
            0 => 
            array (
              'type' => 'Regex',
              'regex' => '^[a-z0-9_]+$',
              'regex_modifiers' => 'im',
              'name' => 'NO_SPECIAL_CHARACTERS',
            ),
          ),
        ),
        'type' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
          'lookup' => 
          array (
            'type' => 'enum',
            'validValues' => 'STRING,NUMBER,BOOLEAN',
          ),
        ),
        'control_type' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
          'lookup' => 
          array (
            'type' => 'enum',
            'validValues' => 'DATE,DATETIME,SELECT,XHRPICKER,INTEGER,DOUBLE,CURRENCY,TEXTBOX,TEXTAREA,CHECKBOX',
          ),
        ),
        'lookup_type' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
          'lookup' => 
          array (
            'type' => 'enum',
            'validValues' => 'OTLT,table,enum,FK,valueMap,valueMapService',
          ),
        ),
        'lookup' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'description' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'textarea',
          ),
        ),
      ),
    ),
    'core.app_lock' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'app_lock',
      ),
      'columns' => 
      array (
        'app_lock_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'reason' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'message' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'from_date' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'to_date' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'active_flag' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'meta' => 
        array (
          'attributes' => 
          array (
          ),
        ),
      ),
    ),
    'core.translation' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'translation',
        'editable' => true,
      ),
      'trigger' => 
      array (
        0 => 
        array (
          'name' => 'activate',
          'language' => 'plpgsql',
          'when' => 'BEFORE INSERT OR UPDATE',
          'body' => '
            
                IF ( NOT(NEW.active_flag) OR NEW.active_flag IS NULL)
                AND (NEW.value IS NOT NULL AND NOT(NEW.value ~ E\'.*?[[T]]$\')) THEN
                        NEW.active_flag := true;
                END IF;
                RETURN NEW;
            
        ',
        ),
      ),
      'columns' => 
      array (
        'translation_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'domain_name' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'locale' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'token' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'value' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'used_flag' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'active_flag' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'expose_flag' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'last_usage_date' => 
        array (
          'attributes' => 
          array (
          ),
        ),
      ),
    ),
    'core.pending_call' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'pending_call',
        'editable' => false,
      ),
      'columns' => 
      array (
        'pending_call_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'sid' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'recording_url' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'client_sid' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'creation_date' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'caller_user_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'target' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'serialized_call' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'properties' => 
        array (
          'attributes' => 
          array (
          ),
        ),
      ),
    ),
    'core.async_job' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'async_job',
        'editable' => false,
      ),
      'columns' => 
      array (
        'async_job_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'issuer_user_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'context' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'executor_type' => 
        array (
          'attributes' => 
          array (
          ),
          'lookup' => 
          array (
            'type' => 'enum',
            'validValues' => 'rundeck',
          ),
        ),
        'execution_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'job_path' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'parameters' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'start_date' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'completion_date' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'completion_percentage' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'outcome' => 
        array (
          'attributes' => 
          array (
          ),
          'lookup' => 
          array (
            'type' => 'enum',
            'validValues' => 'success,failure',
          ),
        ),
        'job_output' => 
        array (
          'attributes' => 
          array (
          ),
        ),
      ),
    ),
    'core.user_notification' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'user_notification',
        'editable' => false,
      ),
      'columns' => 
      array (
        'user_notification_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'user_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'context' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'title' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'notification' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'notification_data' => 
        array (
          'attributes' => 
          array (
          ),
        ),
      ),
    ),
    'core.file_property' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'file_property',
        'editable' => true,
        'defaultLister' => 'Eulogix/Cool/Bundle/CoreBundle/CWidget/Core/FileProperty/FilePropertiesLister',
      ),
      'trigger' => 
      array (
        0 => 
        array (
          'name' => 'DELETE_FIELDS',
          'language' => 'plpgsql',
          'when' => 'AFTER DELETE',
          'body' => '
            DELETE FROM field_definition WHERE field_definition_id = OLD.field_definition_id;
            RETURN NULL; -- ignored, this is an AFTER trigger
        ',
        ),
      ),
      'columns' => 
      array (
        'file_property_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'field_definition_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'context_schema' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'context_actual_schema' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'context_table' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'context_category' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'show_in_list_flag' => 
        array (
          'attributes' => 
          array (
          ),
        ),
      ),
    ),
    'core.user_reminder' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'user_reminder',
        'editable' => true,
      ),
      'columns' => 
      array (
        'user_reminder_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'name' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'category' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
          'lookup' => 
          array (
            'type' => 'table',
            'domainName' => 'CORE_USER_REMINDER_CATEGORY',
          ),
        ),
        'lister' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'lister_translation_domain' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'parent_tables' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'context_schema' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'sql_query' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'textarea',
          ),
        ),
        'type' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
          'lookup' => 
          array (
            'type' => 'enum',
            'validValues' => 'SIMPLE,DATED',
          ),
        ),
        'sort_order' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'count_sql_query' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'textarea',
          ),
        ),
      ),
    ),
    'core.rule' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'rule',
        'editable' => true,
      ),
      'columns' => 
      array (
        'rule_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'name' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'description' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'category' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
          'lookup' => 
          array (
            'type' => 'table',
            'domainName' => 'RULE_CATEGORY',
          ),
        ),
        'expression_type' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
          'lookup' => 
          array (
            'type' => 'enum',
            'validValues' => 'HOA,PHP',
          ),
        ),
        'expression' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'textarea',
          ),
        ),
      ),
    ),
    'core.rule_code' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'rule_code',
        'editable' => true,
        'defaultEditor' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\CWidget\\Core\\Rule\\RuleCodeEditorForm',
      ),
      'columns' => 
      array (
        'rule_code_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'rule_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'enabled_flag' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'type' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
          'lookup' => 
          array (
            'type' => 'enum',
            'validValues' => 'VARIABLE,EXEC_IF_TRUE,EXEC_IF_FALSE',
          ),
        ),
        'name' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'code_snippet_id' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'xhrpicker',
          ),
        ),
        'code_snippet_variables' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'raw_code' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'textarea',
          ),
        ),
      ),
    ),
    'core.code_snippet' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'code_snippet',
        'editable' => true,
        'valueMapDecodingSQL' => 'category || \' - \' || COALESCE(description, name)',
      ),
      'columns' => 
      array (
        'code_snippet_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'category' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
          'lookup' => 
          array (
            'type' => 'table',
            'domainName' => 'CODE_SNIPPET_CATEGORY',
          ),
        ),
        'language' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
          'lookup' => 
          array (
            'type' => 'enum',
            'validValues' => 'PHP',
          ),
        ),
        'type' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
          'lookup' => 
          array (
            'type' => 'enum',
            'validValues' => 'EXPRESSION,FUNCTION_BODY',
          ),
        ),
        'return_type' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
          'lookup' => 
          array (
            'type' => 'enum',
            'validValues' => 'BOOLEAN,STRING,NUMBER,ARRAY,OBJECT,NONE',
          ),
        ),
        'nspace' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'name' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'description' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'long_description' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'lock_updates_flag' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'snippet' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'textarea',
          ),
        ),
      ),
    ),
    'core.code_snippet_variable' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'code_snippet_variable',
        'editable' => true,
      ),
      'columns' => 
      array (
        'code_snippet_variable_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'code_snippet_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'name' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'description' => 
        array (
          'attributes' => 
          array (
          ),
        ),
      ),
    ),
    'core.widget_rule' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'widget_rule',
      ),
      'columns' => 
      array (
        'widget_rule_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'parent_widget_rule_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'widget_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'rule_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'enabled_flag' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'evaluation' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'select',
          ),
          'lookup' => 
          array (
            'type' => 'enum',
            'validValues' => 'ON_LOAD,BEFORE_VALIDATION,BEFORE_DEFINITION,ALWAYS',
          ),
        ),
      ),
    ),
    'core.pg_listener_hook' => 
    array (
      'attributes' => 
      array (
        'schema' => 'core',
        'rawname' => 'pg_listener_hook',
        'editable' => true,
      ),
      'columns' => 
      array (
        'pg_listener_hook_id' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'name' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'channels_regex' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'description' => 
        array (
          'attributes' => 
          array (
          ),
        ),
        'exec_sql_statements' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'textarea',
          ),
        ),
        'exec_sf_command' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'textarea',
          ),
        ),
        'exec_shell_command' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'textarea',
          ),
        ),
        'exec_php_code' => 
        array (
          'attributes' => 
          array (
          ),
          'control' => 
          array (
            'type' => 'textarea',
          ),
        ),
      ),
    ),
  ),
  'views' => 
  array (
  ),
);
    }
    
    /** returns the schema name **/
    public function getSchemaName() {
        return  'core';
    }
    
    public function getNamespace() {
        return  'Eulogix\Cool\Bundle\CoreBundle\Model\Core';
    }

    public function getProjectDir() {
        return  '@EulogixCoolCoreBundle/Resources/databases/core';
    }
       
}