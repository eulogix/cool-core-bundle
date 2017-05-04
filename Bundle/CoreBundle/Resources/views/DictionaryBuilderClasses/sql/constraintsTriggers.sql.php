SET lc_messages TO 'en_US.UTF-8';

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

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

if( !Cool::getInstance()->getSchema($databaseName)->isMultiTenant() ) {
    echo "SET SCHEMA '$databaseName';\n";
}

$dictionary = Cool::getInstance()->getSchema($databaseName)->getDictionary();

foreach($dictionary->getTableNames() as $tbl) {

    $tableConstraints = [];

    $tmap = $dictionary->getPropelTableMap($tbl);
    if($fields = $tmap->getCoolFields()) {
        foreach($fields as $field) {
            if($css = $field->getConstraints()) {
                foreach($css as $constraint) {
                    $tableConstraints[] = array_merge(array('column'=>$field->getName()) ,array(
                        'name'=>$constraint->getName(),
                        'type'=>$constraint->getType(),
                        'regex'=>$constraint->getRegex(),
                        'modifiers'=>$constraint->getRegexModifiers(),
                        //TODO: implement the other constraints
                    ));
                }
            }
        }
    }

    if($tableConstraints) {

        $functionName = "check_constraints_".$tmap->getCoolRawName();

        $sql = "
        CREATE OR REPLACE FUNCTION $functionName()
        RETURNS trigger AS \$\$\n";

        foreach($tableConstraints as $cs) {
            switch($cs['type']) {
                case 'Regex' : {

                    $sql.=" if(!NEW.{$cs['column']}.match(/{$cs['regex']}/{$cs['modifiers']})) {
                       plv8.elog(ERROR, 'error', '{$cs['column']}', '{$cs['name']}');
                    }\n";

                    break;
                }
            }
        }

        $sql.=" return NEW;
        \$\$ LANGUAGE plv8;

        DROP TRIGGER IF EXISTS T_$functionName ON {$tbl};

        CREATE TRIGGER T_$functionName BEFORE INSERT OR UPDATE ON {$tbl}
          FOR EACH ROW
          EXECUTE PROCEDURE $functionName();\n";

        echo $sql;
    }
}

?>
