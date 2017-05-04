<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Dictionary\Lookup;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

$sql = "SET lc_messages TO 'en_US.UTF-8';\n";

if( !Cool::getInstance()->getSchema($databaseName)->isMultiTenant() ) {
    $sql.= "SET SCHEMA '$databaseName';\n";
}

$dictionary = Cool::getInstance()->getSchema($databaseName)->getDictionary();

foreach($dictionary->getTableNames() as $tbl) {

    $tableSql = '';

    $tmap = $dictionary->getPropelTableMap($tbl);
    if($fields = $tmap->getCoolFields()) {
        foreach($fields as $field) {
            if(!$field->isExtension() && ($lookup = $field->getLookup())) {
                switch($lookup->getType()) {
                    case Lookup::TYPE_OTLT : {
                        $tableSql.="
        IF (NEW.".$field->getName()." IS NOT NULL
            AND NOT EXISTS (SELECT lookup_id FROM core.lookup WHERE domain_name='".$lookup->getDomainName()."' AND value=NEW.".$field->getName()." LIMIT 1))
        THEN
            RAISE EXCEPTION 'Invalid value % for column %, lookup %', NEW.".$field->getName().", '".$field->getName()."', '".$lookup->getDomainName()."';
        END IF;\n";
                        break;
                    }
                }
            }
        }
    }

    if($tableSql) {
        $functionName = 'check_lookups_'.$tmap->getCoolRawName();

        $sql.="CREATE OR REPLACE FUNCTION $functionName()
RETURNS trigger AS \$\$
    BEGIN
        $tableSql
        RETURN NEW;
    END;
    \$\$ LANGUAGE plpgsql;

    DROP TRIGGER IF EXISTS T_$functionName ON $tbl;
CREATE TRIGGER T_$functionName
  BEFORE INSERT OR UPDATE
  ON $tbl
  FOR EACH ROW
  EXECUTE PROCEDURE $functionName();\n";
    }
}

echo $sql;



