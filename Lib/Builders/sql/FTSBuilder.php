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

class FTSBuilder extends BaseSqlBuilder {

    /**
     * @var string
     */
    private $preScript, $postScript;

    public function __construct($schemaName) {
        parent::__construct($schemaName);
        $this->buildScripts();
    }

    private function buildScripts() {
        $tableMaps = $this->getDictionary()->getPropelTableMaps();

        $preSQL = "";
        $postSQL = "";

        foreach($tableMaps as $tableMap) {
            if($tableMap->hasFTSFields()) {

                $fields = $tableMap->getCoolFields();

                $viewName = "fts_".$tableMap->getCoolRawName();

                $preSQL.="DROP /*MATERIALIZED*/ VIEW IF EXISTS {$viewName} CASCADE;\n";
                $postSQL.="CREATE /*MATERIALIZED*/ VIEW {$viewName} AS SELECT\n";

                //add all pks and fks to the view, to ease joins
                foreach($fields as $field)
                    if(!$field->isExtension() && ($field->getPropelColumn()->isPrimaryKey() || $field->getPropelColumn()->isForeignKey()))
                        $postSQL.=$field->getName().",\n";

                //then the FTS expression
                $ftsFields = [];
                foreach($fields as $field)
                    if(!$field->isExtension() && $field->isFTSIndexable()) {
                        $ftsFields[] = "COALESCE({$field->getName()},'')";
                    }
                $postSQL.=implode(" || ' ' || ", $ftsFields)." AS search_text\n";

                $postSQL.="FROM {$tableMap->getName()};\n\n";

                //TODO add indexes for FTS
            }
        }

        $this->preScript = $preSQL;
        $this->postScript = $postSQL;
    }

    /**
     * @return string
     */
    public function getPreScript() {
        return $this->getDecoratedSQL($this->preScript);
    }

    /**
     * @return string
     */
    public function getPostScript() {
        return $this->getDecoratedSQL($this->postScript);
    }

}