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
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FixSequencesBuilder extends BaseSqlBuilder {

    public function getScript() {

        $tableMaps = $this->getDictionary()->getPropelTableMaps();

        $sql = "";

        foreach ($tableMaps as $tableName => $tableMap) {
            if($tableMap->isUseIdGenerator()) {
                $sequenceName = $tableMap->getPrimaryKeyMethodInfo();
                $pks = $tableMap->getPrimaryKeys();
                $pkField = array_pop($pks)->getName();
                $sql .= "SELECT setval('{$sequenceName}', COALESCE((SELECT MAX({$pkField})+1 FROM {$tableName}), 1), false);\n";
            }
        }

        return $this->getDecoratedSQL($sql);
    }

}