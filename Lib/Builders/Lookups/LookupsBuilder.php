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

class LookupsBuilder {

    /**
     * @var string
     */
    private $schemaName;

    /**
     * @param string $schemaName
     */
    public function __construct($schemaName) {
        $this->schemaName = $schemaName;
    }

    /**
     * @return Schema
     */
    public function getSchema() {
        return Cool::getInstance()->getSchema($this->schemaName);
    }

    /**
     * @return Dictionary
     */
    public function getDictionary() {
        return Cool::getInstance()->getSchema($this->schemaName)->getDictionary();
    }

    /**
     * @return array
     */
    protected function getLookups()
    {
        $lookups = [];
        $tableMaps = $this->getDictionary()->getPropelTableMaps();
        foreach ($tableMaps as $tableName => $tableMap) {
            $columpMaps = $tableMap->getColumns();
            foreach ($columpMaps as $columnMap) {
                $field = $tableMap->getCoolField($columnMap->getName());

                if ($field && ($lk = $field->getLookup()) ) {
                    switch($type = $lk->getType()) {
                        case Lookup::TYPE_TABLE : {
                            $lookups[$type][$lk->getDomainName()][] = [
                                'table' => $tableName,
                                'field' => $field->getName(),
                                '_lookup' => $lk
                            ];
                            break;
                        }
                        case Lookup::TYPE_ENUM : {
                            $lookups[$type][] = [
                                'table' => $tableMap->getCoolRawName(),
                                'field' => $field->getName(),
                                'validValues' => $lk->getValidValues(),
                                '_lookup' => $lk
                            ];
                            break;
                        }
                    }
                }
            }
        }
        return $lookups;
    }

    /**
     * @return array
     */
    protected function getBaseFields() {
        $fields = [];
        $locales = Cool::getInstance()->getFactory()->getLocaleManager()->getAvailableLocales();
        foreach($locales as $locale) {
            $fields["dec_{$locale}"] = 'TEXT';
        }
        $fields['sort_order'] = 'INTEGER';
        $fields['mandatory_flag'] = 'BOOLEAN';
        $fields['filter'] = 'TEXT[]'; //generic field that can be used to filter the visible portion of the lookup
        $fields['schema_filter'] = 'TEXT[]'; //this field allows to define specific lookup sets for multi tenant schemas
        $fields['original_value'] = 'TEXT'; //used to aid in migration
        $fields['notes'] = 'TEXT'; //used for generic comments on a particular lookup value

        return $fields;
    }

    /**
     * @return array
     */
    protected function getFields() {
        return array_merge(
            array(
            'value' => 'TEXT',
        ),
        $this->getBaseFields());
    }

    /**
     * @return string
     */
    private function getEnumScript() {

        $allLookups = $this->getLookups();
        if(isset($allLookups[Lookup::TYPE_ENUM])) {
            $sql ="SET lc_messages TO 'en_US.UTF-8';\n\n";
            $lookups = $allLookups[Lookup::TYPE_ENUM];

            foreach($lookups as $rel) {
                $fieldName = $rel['field'];
                $tableName = $rel['table'];
                $constraintName = $tableName.'_enum_'.$fieldName;
                $sql.="
                        ALTER TABLE {$rel['table']} DROP CONSTRAINT IF EXISTS $constraintName;
                        ALTER TABLE {$rel['table']} ADD CONSTRAINT $constraintName CHECK ($fieldName IN('".implode("','",$rel['validValues'])."'));
                 ";
            }
            return $sql;
        }

        return '';
    }

    /**
     * @return string
     */
    private function getTableScript()
    {
        $allLookups = $this->getLookups();
        if(isset($allLookups[Lookup::TYPE_TABLE])) {
            $sql ="SET lc_messages TO 'en_US.UTF-8';\n\n";
            $lookups = $allLookups[Lookup::TYPE_TABLE];
            $fields = $this->getFields();

            foreach($lookups as $domainName => $relations) {
                //1. create missing lookup tables with default set of parameters
                $tn = strtolower($domainName);
                $sql.= "CREATE TABLE IF NOT EXISTS lookups.$tn
                    (";

                foreach($fields as $fieldName => $fieldDDL) {
                    $sql.="$fieldName $fieldDDL,\n";
                }

                $sql.="PRIMARY KEY (value)
                    );\n\n";

                //2. add all the base fields, this ensures that existing lookup tables do get updated when new base fields are
                //   subsequently added (for instance when adding locales to an existing installation)
                foreach($fields as $fieldName => $fieldDDL) {
                    $sql.="DO $$ BEGIN ";
                    $sql.="ALTER TABLE lookups.$tn ADD COLUMN $fieldName $fieldDDL;";
                    $sql.=" EXCEPTION WHEN OTHERS THEN END; $$;\n";
                }

                //3. create foreign keys to lookup tables
                foreach($relations as $rel) {
                    /** @var Lookup $lookup */
                    $lookup = $rel['_lookup'];
                    if(!$lookup->isMultiple()) {
                        $csName = str_replace('.','_',"{$rel['table']}_{$rel['field']}_FK");
                        $sql.="
     ALTER TABLE {$rel['table']} DROP CONSTRAINT IF EXISTS $csName;
     ALTER TABLE {$rel['table']} ADD CONSTRAINT $csName
                            FOREIGN KEY ({$rel['field']})
                            REFERENCES lookups.{$tn} (value)
                            ON DELETE RESTRICT
                            ON UPDATE CASCADE;
                            ";
                    }
                }
            }
            return $sql;
        }

        return '';
    }

    /**
     * @param string $targetFile
     */
    public function outputTableScript($targetFile) {
        if($script = $this->getTableScript()) {
            file_put_contents($targetFile, $script);
        }
    }

    /**
     * @param string $targetFile
     */
    public function outputEnumScript($targetFile) {
        if($script = $this->getEnumScript()) {
            file_put_contents($targetFile, $script);
        }
    }

    /**
     * @param $projectDir
     * @param $targetFile
     */
    public function outputFixtures($projectDir, $targetFile) {
        $sql ="SET lc_messages TO 'en_US.UTF-8';\n\n";

        $fixtureFile = $projectDir.DIRECTORY_SEPARATOR.'lookup_fixtures.csv';

        //load or create the csv file
        $fixtures = $this->csv_to_array($fixtureFile);
        if(!$fixtures) {
            $ak = array_keys($this->getFields());
            $fixtures = [array_merge(['domain'=>'domain'],array_combine($ak, $ak))];
        }
        $existingDomains = array_column($fixtures,'domain');

        //determine which new lookups we should add to our csv file as empty rows
        $allLookups = $this->getLookups();
        if(isset($allLookups[Lookup::TYPE_TABLE])) {
            $lookups = $allLookups[Lookup::TYPE_TABLE];
            foreach($lookups as $domainName => $relations) {
                if(!in_array($domainName, $existingDomains))
                    $fixtures[] = ['domain'=>$domainName, 'value'=>''];
            }
        }

        //$fixtures = array_orderby($fixtures, 'domain', SORT_ASC, 'value', SORT_ASC);
        $this->array_to_csv($fixtures, $fixtureFile);

        //build the sql for database insertion
        $i = 0;
        foreach($fixtures as $fixture) {
            $insFixture = [];
            $rowFields = [];
            $insertRow = false;
            foreach($fixture as $field => $value) {
                switch($field) {
                    case 'domain' : break;
                    case 'sort_order' : { $insFixture[] = $value ? $value : 'NULL'; $rowFields[] = $field; break; }
                    case 'mandatory_flag' : { $insFixture[] = $value ? "TRUE" : "FALSE"; $rowFields[] = $field; break; }
                    case 'value' : { $insertRow = !empty($value); }
                    default: { $insFixture[] = $value ? "'".str_replace("'","''",$value)."'" : 'NULL'; $rowFields[] = $field; }
                }
            }
            if($insertRow && $i++>0) {
                $sql.="DO $$ BEGIN";
                $sql.=" INSERT INTO lookups.\"".strtolower($fixture['domain'])."\" (".implode(',',$rowFields).") VALUES (".implode(',',$insFixture)."); ";
                $sql.="EXCEPTION WHEN OTHERS THEN END; $$;\n";
            }
        }

        file_put_contents($targetFile, $sql);
    }

    /**
     * @param string $filename
     * @param string $delimiter
     * @return array|bool
     */
    private function csv_to_array($filename='', $delimiter=';')
    {
        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;

        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
            {
                if(!$header)
                    $header = $row;
                $data[] = $this->array_combine_special($header, $row);
            }
            fclose($handle);
        }
        return $data;
    }

    /**
     * @param array $array
     * @param string $filename
     * @param string $delimiter
     */
    private function array_to_csv($array, $filename='', $delimiter=';')
    {
        $fp = fopen($filename, 'w');

        $header = array_keys(array_shift($array));

        $array = array_orderby($array, 'domain', SORT_ASC, 'value', SORT_ASC);

        fputcsv($fp, $header, $delimiter);

        foreach ($array as $rowid=>$fields) {
            fputcsv($fp, $fields, $delimiter);
        }

        fclose($fp);
    }

    /**
     * @param array $a
     * @param array $b
     * @param bool $pad
     * @return array
     */
    private function array_combine_special($a, $b, $pad = TRUE) {
        $acount = count($a);
        $bcount = count($b);
        // more elements in $a than $b but we don't want to pad either
        if (!$pad) {
            $size = ($acount > $bcount) ? $bcount : $acount;
            $a = array_slice($a, 0, $size);
            $b = array_slice($b, 0, $size);
        } else {
            // more headers than row fields
            if ($acount > $bcount) {
                $more = $acount - $bcount;
                // how many fields are we missing at the end of the second array?
                // Add empty strings to ensure arrays $a and $b have same number of elements
                $more = $acount - $bcount;
                for($i = 0; $i < $more; $i++) {
                    $b[] = "";
                }
                // more fields than headers
            } else if ($acount < $bcount) {
                $more = $bcount - $acount;
                // fewer elements in the first array, add extra keys
                for($i = 0; $i < $more; $i++) {
                    $key = 'extra_field_0' . $i;
                    $a[] = $key;
                }

            }
        }

        return array_combine($a, $b);
    }
}