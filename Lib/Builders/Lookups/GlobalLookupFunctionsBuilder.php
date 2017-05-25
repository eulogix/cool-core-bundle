<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Builders\Lookups;

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Database\Schema;
use Eulogix\Cool\Lib\Dictionary\Dictionary;
use Eulogix\Cool\Lib\Dictionary\Lookup;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class GlobalLookupFunctionsBuilder {

    public function __construct() {


    }

    public function getLookupDomainFunction() {
        $get_lookup_domain = "CREATE OR REPLACE FUNCTION core.get_lookup_domain(schema_name text, table_name text, column_name text) RETURNS TEXT AS $$\n\tswitch(schema_name) {\n";

        $schemas = Cool::getInstance()->getAvailableSchemas();
        foreach($schemas as $schemaName => $schemaNs) {

            $get_lookup_domain.= "\t\tcase '{$schemaName}' : {\n";

            $schema = Cool::getInstance()->getSchema($schemaName);
            $dict = $schema->getDictionary();
            $tables = $dict->getTableNames();

            $get_lookup_domain.= "\t\t\tswitch(table_name) {\n";

            foreach($tables as $tableName) {
                $tableMap = $dict->getPropelTableMap($tableName);

                $get_lookup_domain.= "\t\t\t\tcase '{$tableMap->getCoolRawName()}' : {\n";
                $get_lookup_domain.= "\t\t\t\t\tswitch(column_name) {\n";

                $columns = $tableMap->getColumns();
                foreach($columns as $col) {
                    $coolField = $tableMap->getCoolField($col->getName());

                    if (($lookup = $coolField->getLookup()) && $lookup->getDomainName()) {
                        $get_lookup_domain.= "\t\t\t\t\t\tcase '{$coolField->getName()}' : return '{$lookup->getDomainName()}';\n";
                    }
                }
                $get_lookup_domain.= "\t\t\t\t\t}\n";

                $get_lookup_domain.= "\t\t\t\tbreak; }\n";

            }

            $get_lookup_domain.= "\t\t\tbreak; }\n";

            $get_lookup_domain.= "\t\tbreak; }\n";
        }

        $get_lookup_domain.= "\tbreak; }\n\n$$ LANGUAGE plv8;";

        return $get_lookup_domain;
    }

}