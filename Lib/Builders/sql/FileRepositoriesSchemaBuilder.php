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

class FileRepositoriesSchemaBuilder extends BaseSqlBuilder {

    /**
     * @return string
     */
    public function getScript() {
        $tableMaps = $this->getDictionary()->getPropelTableMaps();

        $tplData = ['tableMaps'=>$tableMaps, 'globalSchemaName'=>$this->getSchema()->getName()];

        $sql = $this->processAsTwigTemplate(file_get_contents(__DIR__.'/FileRepositoriesSchema.sql'), $tplData);

        return $this->getDecoratedSQL($sql);
    }

}