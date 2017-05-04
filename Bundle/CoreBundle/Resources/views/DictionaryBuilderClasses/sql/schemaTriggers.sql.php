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
    echo  "SET SCHEMA '$databaseName';\n";
}

$dictionary = Cool::getInstance()->getSchema($databaseName)->getDictionary();

foreach($dictionary->getTableNames() as $tbl) {
    
    $tmap = $dictionary->getPropelTableMap($tbl);

    if($triggers = $tmap->getCoolTriggers()) {
        foreach($triggers as $trigger) {
            
            $functionName =  $tmap->getCoolRawName().'_'.$trigger->getName();
               
            $sql = "
            CREATE OR REPLACE FUNCTION $functionName() 
            RETURNS trigger AS \$\$
                {$trigger->getBody()}
            \$\$ LANGUAGE {$trigger->getLanguage()};

            DROP TRIGGER IF EXISTS T_$functionName ON {$tbl};

            CREATE TRIGGER T_$functionName {$trigger->getWhen()} ON {$tbl}
              FOR EACH ROW
              EXECUTE PROCEDURE $functionName();\n";

            echo $sql;
                
        }
    }
}
?>
