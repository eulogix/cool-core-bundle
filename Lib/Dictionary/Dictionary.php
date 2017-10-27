<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Dictionary;

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionFieldQuery;
use Eulogix\Cool\Lib\Database\Propel\CoolTableMap;
use Eulogix\Cool\Lib\Form\Field\FieldInterface;
use Eulogix\Lib\Cache\Cached;

/**
 * Provides convenience method to deal with custom database properties
 * 
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class Dictionary {

    use Cached;

    const TBL_ATT_EDITABLE   = 'editable';      
    const TBL_ATT_SCHEMA = 'schema';
    const TBL_ATT_RAWNAME = 'rawname';
    const TBL_ATT_NAMESPACE = 'namespace';
    const TBL_ATT_PHP_NAME = 'phpName';

    const TBL_ATT_DEFAULT_LISTER = 'defaultLister';
    const TBL_ATT_DEFAULT_EDITOR = 'defaultEditor';
    const TBL_ATT_VALUE_MAP_CLASS = 'valueMapClass';
    const TBL_ATT_VALUE_MAP_DECODING_SQL = 'valueMapDecodingSQL';
    const TBL_ATT_VALUE_MAP_SEARCH_SQL = 'valueMapSearchSQL';
    const TBL_ATT_AUDIT_ID = 'auditId';

    const TBL_ATT_TRIGGERS = 'trigger';
    const TBL_ATT_SQL_SNIPPETS = 'customSQL';

    const TBL_ATT_FILES = 'files';
    const TBL_ATT_FILES_CATEGORY = 'category';

    const COL_ATT_CONSTRAINTS         = 'constraint';
    
    const COL_ATT_SOURCE              = 'source';
    const COL_ATT_SOURCE_DB_EXTENSION = 'db_extension';
    const COL_ATT_SOURCE_DB_EXTENSION_CONTAINER = 'container';
    const COL_ATT_TABLE_EXTENSION_FIELD_ID = 'table_extension_field_id';

    const COL_ATT_CONTROL_CONTAINER              = 'control';
    const COL_ATT_CONTROL_TYPE              = 'type';
    const COL_ATT_CALCULATED = 'calculated';
    const COL_ATT_EDITABLE = 'editable';
    const COL_ATT_FTS = 'fts';

    const VIEW_ATT_TABLES         = 'table';

    /**
    * dictionary settings (in the overridden class, or by dynamic db extensions, can depend on the schema too
    * 
    * @var mixed
    */
    protected $schemaName;


    public function __construct( $schemaName = null ) {
        $this->schemaName = $schemaName;    
    }

    public function getTableNamespace($tableName) {
        return $this->getTableAttribute($tableName, self::TBL_ATT_NAMESPACE) ??
               $this->getNamespace();
    }

    /**
    * returns the Propel Model class namespace defined for the given table name
    * 
    * @param mixed $tableName
    * @returns string
    */
    public function getPropelModelClassNamespace($tableName) {
        $namespace = $this->getTableNamespace($tableName);
        return "$namespace\\".$this->getPhpName($tableName);
    }

    /**
    * returns the Propel Peer namespace defined for the given table name
    *
    * @param mixed $tableName
    * @returns string
    */
    public function getPropelPeerNamespace($tableName) {
        $namespace = $this->getTableNamespace($tableName);
        return "$namespace\\".$this->getPhpName($tableName).'Peer';
    }

    /**
    * returns the Propel Query namespace defined for the given table name
    *
    * @param mixed $tableName
    * @returns string
    */
    public function getPropelQueryNamespace($tableName) {
        $namespace = $this->getTableNamespace($tableName);
        return "$namespace\\".$this->getPhpName($tableName).'Query';
    }

    /**
    * @param mixed $tableName
    * @returns string
    */
    public function getPhpName($tableName) {
        return $this->getTableAttribute($tableName, self::TBL_ATT_PHP_NAME) ??
               \Eulogix\Cool\Lib\Database\Propel\Util::camelize( $this->getTableRawName( $tableName ) );
    }
    
    /**
    * returns the runtime propel tablemap for the given table
    * 
    * @param mixed $tableName
    * @return CoolTableMap
    */
    public function getPropelTableMap($tableName) {
        if($this->hasTable($tableName) && ($peerNs = $this->getPropelPeerNamespace($tableName)))
            return $peerNs::getTableMap();

        //make this work for non multitenant schemas when only the tablename is provided without the schema specifier
        if( $ret = $this->getPropelTableMap($this->schemaName.'.'.$tableName))
            return $ret;

        return false;
    }

    /**
     * returns an array of table maps
     * @return CoolTableMap[]
     */
    public function getPropelTableMaps() {
        $tmaps = [];
        foreach($this->getTableNames() as $tableName)
            $tmaps[$tableName] = $this->getPropelTableMap($tableName);
        return $tmaps;
    }

    /**
     * @return View[]
     */
    public function getViews() {
        $ret = [];
        $viewNames = $this->getViewNames();
        foreach($viewNames as $viewName) {
            $v = new View($this);
            $v->populate($this->getViewSettings($viewName)['attributes']);
            $ret[$viewName] = $v;
        }
        return $ret;
    }

    /**
     * returns an array of view names
     * @return array []
     */
    public function getViewNames() {
        return array_keys( $this->getSettings()['views'] );
    }

    /**
     * returns the raw settings of a given view
     *
     * @param $viewName
     * @return mixed []
     */
    public function getViewSettings($viewName) {
        return $this->getSettings()['views'][$viewName];
    }

    /**
     * returns an array of table names
     * @return array []
     */
    public function getTableNames() {
        return array_keys( $this->getSettings()['tables'] );
    }


    /**
    * checks wether a given table name exists or not
    * 
    * @param mixed $tableName
    * @return boolean
    */
    public function hasTable($tableName) {
        return isset( $this->getSettings()['tables'][$tableName] );
    }
    
    /**
    * checks wether a given column name exists or not
    * 
    * @param mixed $tableName
    * @param mixed $columnName
    * @return boolean
    */
    public function hasColumn($tableName, $columnName) {
        return isset( $this->getTableColumns($tableName)[$columnName] );
    }

    /**
     * @param string $tableName
     * @return string
     */
    public function getTableRawName($tableName) {
        return $this->getTableAttribute($tableName, self::TBL_ATT_RAWNAME);
    }

    /**
    * returns an associative array of attributes for a given table
    *
    * @param mixed $tableName
    * @return []
    */
    public function getTableAttributes($tableName) {
        return $this->getSettings()['tables'][$tableName]['attributes'];
    }

    /**
    * returns an associative array of triggers for a given table
    *
    * @param mixed $tableName
    * @return []
    */
    public function getTableTriggers($tableName) {
        return $this->getSettings()['tables'][$tableName][self::TBL_ATT_TRIGGERS];
    }

    /**
     * returns an associative array of triggers for a given table
     *
     * @param mixed $tableName
     * @return array []
     */
    public function getTableFilesCategories($tableName) {
        return @$this->getSettings()['tables'][$tableName][self::TBL_ATT_FILES][self::TBL_ATT_FILES_CATEGORY];
    }

    /**
    * returns an associative array of custom SQL snippets for a given table
    *
    * @param mixed $tableName
    * @return []
    */
    public function getTableSQLSnippets($tableName) {
        return $this->getSettings()['tables'][$tableName][self::TBL_ATT_SQL_SNIPPETS];
    }

    /**
    * returns the value of an attribute for a given table
    * 
    * @param string $tableName
    * @param mixed $attributeName
    * @return mixed
    */
    public function getTableAttribute($tableName,$attributeName) {
        return @$this->getTableAttributes($tableName)[ $attributeName ];
    }

    /**
     * returns an array of column names for a given table
     *
     * @param string $tableName
     * @param string $physicalSchemaName
     * @return array []
     */
    public function getTableColumns($tableName, $physicalSchemaName=null) {
        $c = $this->getCacher();
        $tk = $c->tokenize([__METHOD__, $tableName, $physicalSchemaName]);
        if(!$c->exists($tk)) {
            $ret = array_merge(
                $this->getSettings()['tables'][$tableName]['columns'],
                $this->getExtendedTableColumns($tableName, $physicalSchemaName)
            );
            $c->store($tk, $ret);
        }
        return $c->fetch($tk);
    }

    /**
     * returns any defined table extension in cool_core schema
     *
     * @param string $tableName
     * @param string $physicalSchemaName
     * @return array
     */
    public function getExtendedTableColumns($tableName, $physicalSchemaName=null) {
        $coolSchema = Cool::getInstance()->getCoreSchema();
        $extensions = $coolSchema->getTableExtensions($tableName, $physicalSchemaName);
        $ret = [];
        foreach($extensions as $ext) {

            $tableExtensionField = TableExtensionFieldQuery::create()->findPk($ext['table_extension_field_id']);
            $def = $tableExtensionField->getFieldDefinition();

            $ret[$ext['name']] = [
                self::COL_ATT_CONTROL_CONTAINER => [
                    self::COL_ATT_CONTROL_TYPE => $def ? $def->getCoolFieldType() : FieldInterface::TYPE_TEXTBOX
                ],
                self::COL_ATT_SOURCE => self::COL_ATT_SOURCE_DB_EXTENSION,
                self::COL_ATT_SOURCE_DB_EXTENSION_CONTAINER => $ext['container'],
                self::COL_ATT_TABLE_EXTENSION_FIELD_ID => $tableExtensionField->getPrimaryKey()
            ];

        }

        return $ret;
    }

    /**
     * returns an associative array of attributes for a given column
     *
     * @param mixed $tableName
     * @param mixed $columnName
     * @return array []
     */
    public function getColumnAttributes($tableName,$columnName) {
        $ret = $this->getTableColumns($tableName)[ $columnName ];
        return $ret;
    }
    
    /**
    * returns the value of an attribute for a given table
    *
    * @param string $tableName
    * @param mixed $columnName
    * @param mixed $attributeName
    * @return mixed
    */
    public function getColumnAttribute($tableName,$columnName,$attributeName) {
        $inlineAttributes = @$this->getTableColumns($tableName)[ $columnName ]['attributes'];
        if(!is_array($inlineAttributes))
            $inlineAttributes = [];

        if(@$inlineAttributes[$attributeName])
            return $inlineAttributes[$attributeName];

        $nestedAttributes = $this->getTableColumns($tableName)[ $columnName ];
        if(!is_array($nestedAttributes))
            $nestedAttributes = [];

        if(@$nestedAttributes[$attributeName])
            return $nestedAttributes[$attributeName];

    }

    
    /**
    * must be implemented by the generated baseDictionary that extends this class
    * @return []
    */
    abstract public function getSettings(); 
    
    /**
    * must be implemented by the generated baseDictionary that extends this class
    * @return []
    */
    abstract public function getProjectDir();

    /**
    * must be implemented by the generated baseDictionary that extends this class
    * @return []
    */
    abstract public function getNamespace();
    
    /**
    * returns the schema which the related Database Object is bound to
    * 
    */
    public function getSchemaName() {
        return $this->schemaName;
    }
}  