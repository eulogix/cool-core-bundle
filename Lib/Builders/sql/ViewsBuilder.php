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
use Eulogix\Cool\Lib\Dictionary\View;
use Eulogix\Cool\Lib\Dictionary\ViewTable;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ViewsBuilder {

    /**
     * @var string
     */
    private $databaseName;

    /**
     * @var string
     */
    protected $dropScript = '';

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
     * @param string $targetFile
     */
    public function outputDropScript($targetFile) {
        if($script = $this->dropScript) {
            file_put_contents($targetFile, $script);
        }
    }

    /**
     * @return Dictionary
     */
    protected function getDictionary() {
        return Cool::getInstance()->getSchema($this->databaseName)->getDictionary();
    }

    /**
     * @return string
     */
    public function getDropAllViewsSql() {
        $sqlStatements = [];
        $views = $this->getDictionary()->getViews();
        foreach($views as $dbView) {
            $sqlStatements[] = "DROP VIEW IF exists {$dbView->getName()} CASCADE;";
        }
        return implode("\n", $sqlStatements);
    }

    /**
     * @return string
     */
    public function getCreateAllViewsSql() {
        $sqlStatements = [];
        $views = $this->getDictionary()->getViews();
        foreach($views as $dbView) {
            $sqlStatements[] = $this->getCreateViewSql($dbView);
        }
        return implode("\n\n", $sqlStatements);
    }

    /**
     * @param View $dbView
     * @return string
     */
    private function getCreateViewSql(View $dbView)
    {
        $alreadySelectedFields = [];
        $createSelectTokens = [];
        $newTokens = [];
        $joinTokens = [];
        $columnNames = [];
        $firstTable = null;

        $first = true;
        $tables = $dbView->getTables();
        foreach($tables as $t) {
            $alias = $t->getAlias() ? $t->getAlias() : $t->getName();
            $duplicatePrefix = $t->getDuplicatePrefix();

            if($first) {
                $fromSql = $t->getName().' '.$alias;
                $firstTable = $t;
                $pkf = $firstTable->getPropelTableMap()->getPkFields();
                $idCol = array_pop($pkf);
                $first = false;
            } else {
                $joinTokens[] = "JOIN ".$t->getName().' '.$alias." ".$t->getJoin();
            }
            //we fetch only physical columns in views
            $fields = $t->getPropelTableMap()->getColumns();
            foreach($fields as $field) {
                //prefix already selected fields with the duplicate prefix, or the table name if not specified
                $viewFieldName = !in_array($field->getName(), $alreadySelectedFields) ? $field->getName() : $duplicatePrefix . $field->getName();
                $alreadySelectedFields[] = $field->getName();

                $createSelectTokens[] = "$alias.".$field->getName()." AS $viewFieldName";

                $columnNames[$t->getName()][] = $field->getName();

                $newTokens[$t->getName()][] = $field->getName() == $idCol ? ($t == $firstTable ? "DEFAULT":"lastid") : "NEW.".$viewFieldName;
            }
        }

        $createSql = "CREATE VIEW ".$dbView->getName()." AS SELECT ".implode($createSelectTokens, ",\n").
            "\n\tFROM ".$fromSql."\n".
            implode($joinTokens,"\n\t\t").";";

        //TRIGGER
        $functionName = $dbView->getName().'_vw_dml';

        $createSql.= "
        CREATE OR REPLACE FUNCTION $functionName()

RETURNS TRIGGER
LANGUAGE plpgsql
AS \$function\$
DECLARE
    lastid INTEGER;
   BEGIN
      IF TG_OP = 'INSERT' THEN
	    INSERT INTO ".$firstTable->getName()." (".implode(',',$columnNames[$firstTable->getName()]).") VALUES (".implode(',',$newTokens[$firstTable->getName()]).") RETURNING ".$idCol." INTO lastid;";
        foreach($tables as $t) {
            if($t->getName()!=$firstTable->getName()) {
                $createSql.="
	    INSERT INTO ".$t->getName()." (".implode(',',$columnNames[$t->getName()]).") VALUES (".implode(',',$newTokens[$t->getName()]).");\n";
            }
        }

        $createSql.="
        RETURN NEW;/*
      ELSIF TG_OP = 'UPDATE' THEN
       UPDATE person_detail SET pid=NEW.pid, pname=NEW.pname WHERE pid=OLD.pid;
       UPDATE person_job SET pid=NEW.pid, job=NEW.job WHERE pid=OLD.pid;
       RETURN NEW;
      ELSIF TG_OP = 'DELETE' THEN
       DELETE FROM person_job WHERE pid=OLD.pid;
       DELETE FROM person_detail WHERE pid=OLD.pid;
       RETURN NULL;*/
      END IF;
      RETURN NEW;

    END;
\$function\$;

CREATE TRIGGER {$functionName}_trig
    INSTEAD OF INSERT ON
      {$dbView->getName()} FOR EACH ROW EXECUTE PROCEDURE $functionName();";

        $this->dropScript.="DROP FUNCTION IF EXISTS {$functionName}() CASCADE;\n";

        return $createSql;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getScript() {
        $sql ="SET lc_messages TO 'en_US.UTF-8';\n\n";

        if( !Cool::getInstance()->getSchema($this->databaseName)->isMultiTenant() ) {
            $sql.= "SET SCHEMA '{$this->databaseName}';\n";
        }

        $sql .= $this->getDropAllViewsSql()."\n\n";
        $sql .= $this->getCreateAllViewsSql()."\n\n";

        return $sql;
    }
}