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
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\Account',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountQuery',
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
          'name' => 'hash_password',
          'language' => 'plpgsql',
          'when' => 'BEFORE INSERT OR UPDATE',
          'body' => '
            
                    NEW.hashed_password = md5(NEW.password);
                    /*NEW.password=\'\'hidden\'\';*/
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
        'password' => 
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
      ),
    ),
    'core.app_setting' => 
    array (
      'attributes' => 
      array (
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AppSetting',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AppSettingPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AppSettingQuery',
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
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountSetting',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountSettingPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountSettingQuery',
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
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountProfile',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountProfilePeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountProfileQuery',
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
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountProfileSetting',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountProfileSettingPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountProfileSettingQuery',
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
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountProfileRef',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountProfileRefPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountProfileRefQuery',
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
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountGroup',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountGroupPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountGroupQuery',
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
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountGroupRef',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountGroupRefPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountGroupRefQuery',
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
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FormConfig',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FormConfigPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FormConfigQuery',
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
      ),
    ),
    'core.form_config_field' => 
    array (
      'attributes' => 
      array (
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FormConfigField',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FormConfigFieldPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FormConfigFieldQuery',
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
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\ListerConfig',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\ListerConfigPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\ListerConfigQuery',
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
      ),
    ),
    'core.lister_config_column' => 
    array (
      'attributes' => 
      array (
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\ListerConfigColumn',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\ListerConfigColumnPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\ListerConfigColumnQuery',
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
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\Lookup',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\LookupPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\LookupQuery',
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
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\TableExtension',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\TableExtensionPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\TableExtensionQuery',
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
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\TableExtensionField',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\TableExtensionFieldPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\TableExtensionFieldQuery',
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
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FieldDefinition',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FieldDefinitionPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FieldDefinitionQuery',
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
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AppLock',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AppLockPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AppLockQuery',
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
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\Translation',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\TranslationPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\TranslationQuery',
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
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\PendingCall',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\PendingCallPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\PendingCallQuery',
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
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AsyncJob',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AsyncJobPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AsyncJobQuery',
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
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\UserNotification',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\UserNotificationPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\UserNotificationQuery',
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
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FileProperty',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FilePropertyPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FilePropertyQuery',
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
        'propelModelNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\UserReminder',
        'propelPeerNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\UserReminderPeer',
        'propelQueryNamespace' => 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\UserReminderQuery',
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
        'category' => 
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
    
    public function getProjectDir() {
        return  '@EulogixCoolCoreBundle/Resources/databases/core';
    }
       
}