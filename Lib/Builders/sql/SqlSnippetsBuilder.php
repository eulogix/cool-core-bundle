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

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Dictionary\Dictionary;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class SqlSnippetsBuilder {

    /**
     * @var string
     */
    private $databaseName;

    public function __construct($databaseName) {
        $this->databaseName = $databaseName;
    }

    /**
     * @param string $targetFile
     */
    public function output($targetFile) {
        if($script = $this->getScript()) {
            file_put_contents($targetFile, $script);
        }
    }

    /**
     * @return Dictionary
     */
    protected function getDictionary() {
        return Cool::getInstance()->getSchema($this->databaseName)->getDictionary();
    }

    public function getScript() {
        $sql ="SET lc_messages TO 'en_US.UTF-8';\n\n";

        if( !Cool::getInstance()->getSchema($this->databaseName)->isMultiTenant() ) {
            $sql.= "SET SCHEMA '{$this->databaseName}';\n";
        }

        $found = 0;
        if($dictionary = $this->getDictionary()) {
            foreach($dictionary->getTableNames() as $tbl) {
                //TODO: manage the position and the order
                if($snippets = $dictionary->getTableSQLSnippets($tbl)) {
                    foreach($snippets as $snippet) {
                        $sql.=$snippet['body']."\n\n";
                    }
                    $found++;
                }
            }
        }

        return $found > 0 ? $sql : '';
    }

}