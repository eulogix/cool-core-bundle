<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Builders\sql;

/**
 * This class builds a sql script that creates and initializes the <schemaName>_audit schema, which contains both
 * app events and row-level audit trails for all the tables and fields flagged as auditable in the dictionary.
 * A set of audit functions is appended to the main schema
 *
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class AuditSchemaBuilder extends BaseSqlBuilder {

    const LOG_TABLE_NAME = 'row_audit_trail';
    const APP_LOG_TABLE_NAME = 'app_events';

    /**
     * @return string
     */
    public function getScript() {

        $tableMaps = $this->getDictionary()->getPropelTableMaps();

        $sql = $this->processAsTwigTemplate(file_get_contents(__DIR__.'/AuditSchemaMainBody.sql'), [
                'logTableName' => self::LOG_TABLE_NAME,
                'appLogTableName' => self::APP_LOG_TABLE_NAME,
                'tableMaps' => $tableMaps
            ]);

        foreach ($tableMaps as $tableName => $tableMap) {
            $sql .= "
-- set default audit strategy to none
SELECT {{ auditSchema }}._set_audit_strategy_settings('$tableName', 'none', 0, FALSE);
            ";
        }

        return $this->getDecoratedSQL($sql);
    }

}