<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core;

use Eulogix\Cool\Lib\File\FileProperty;

class Schema extends \Eulogix\Cool\Lib\Database\Schema {
    
   public function getSiblingSchemas() {
       return array('core');
   }
   
   public function getCurrentSchema() {
       return 'core';
   }

    /**
     * @param string $tableName
     * @param string $physicalSchemaName
     * @return array|mixed
     */
   public function getTableExtensions($tableName, $physicalSchemaName=null) {
       try {
           $stmt = "SELECT a.*,b.*,fd.*, 'ext' AS container
                FROM table_extension a
                LEFT JOIN table_extension_field b ON (b.table_extension_id=a.table_extension_id)
                LEFT JOIN field_definition fd ON (fd.field_definition_id=b.field_definition_id)
                WHERE a.active_flag AND (b.table_extension_field_id IS NOT NULL)";

           if($tableName) { $stmt.=" AND a.db_table=:tableName"; }
           if($physicalSchemaName) { $stmt.=" AND (a.db_schema=:schemaName OR a.db_schema IS NULL)"; }

           $ret = $this->fetchArray($stmt, array(":tableName"=>$tableName, ":schemaName"=>$physicalSchemaName), true, 60*5);
       } catch(\Exception $e) {
           return [];
       }
       return $ret;
   }

    /**
     * @param string $schema
     * @param string $actualSchema
     * @param string $tableName
     * @param string $category
     * @param bool $deep
     * @return \Eulogix\Cool\Lib\File\FileProperty[]
     */
   public function getAvailableFileProperties($schema, $actualSchema=null, $tableName=null, $category=null, $deep=false) {
       $stmt = "SELECT file_property_id FROM file_property WHERE context_schema=:schema";

       $stmt .= " AND (context_actual_schema IS NULL" . ( $actualSchema ? " OR context_actual_schema=:actualSchema)" : ")" );
       $stmt .= " AND (context_table IS NULL" . ( $tableName ? " OR context_table=:tableName)" : ")" );

       if($deep) {
           $stmt .= $category ? " AND context_category=:category" : "";
           $stmt .= " AND COALESCE(:category, '1') IS NOT NULL";
       } else {
           $stmt .= " AND (context_category IS NULL" . ( $category ? " OR context_category=:category)" : ")" );
       }

       $ids = $this->fetchArrayWithNumericKeys($stmt, [
           ":schema"=>$schema,
           ":actualSchema"=>$actualSchema,
           ":tableName"=>$tableName,
           ":category"=>$category
       ], true, 60*5);

       $propelObjects = FilePropertyQuery::create()->findPks($ids);
       $ret = [];
       foreach($propelObjects as $po) {
           $ret[$po->getFieldDefinition()->getName()] = $po->getCoolFileProperty();
       }
       return $ret;
   }

}
