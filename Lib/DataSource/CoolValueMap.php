<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\DataSource;

use Eulogix\Cool\Lib\Traits\CoolCacheShimmed;
use Eulogix\Lib\Cache\Shimmable;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Database\Propel\CoolTableMap;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CoolValueMap extends BaseValueMap implements Shimmable
{
    use CoolCacheShimmed;

    /**
     * @var array
     */
    protected $allowedValues;

    /**
     * @var string
     */
    private $schemaName, $tableName;

    /**
     * @param string $schemaName
     * @param string $tableName
     * @throws \Exception
     */
    public function __construct($schemaName, $tableName) {
        $this->schemaName = $schemaName;
        $this->tableName = $tableName;
    }

    /**
     * @inheritdoc
     */
    public function getShimUID() {
        return md5(get_class($this).Cool::getInstance()->getSchema($this->schemaName)->getCurrentSchema().';'.$this->tableName);
    }

    /**
     * factory that retrieves and configures a value map for a given column/table/schema
     * if a pk is supplied, it may be used by the instance to further limit/configure its behavior
     *
     * @param $schemaName
     * @param $tableName
     * @param $columnName
     * @param null $recordId
     * @return \Eulogix\Cool\Lib\DataSource\ValueMapInterface
     */
    public static function getValueMapFor($schemaName, $tableName, $columnName, $recordId=null) {
        $map = null;

        try {
            $schema = Cool::getInstance()->getSchema($schemaName);
            $tableMap = $schema->getDictionary()->getPropelTableMap($tableName);
            $coolField = $tableMap->getCoolField($columnName);

            if ($lookup = $coolField->getLookup()) {
                $map = $lookup->getValueMap();
            } else {
                $propelColumn = $tableMap->getColumn($columnName);
                if ($propelColumn->isForeignKey()) {
                    //we fetch the  referenced table, $relation has to be instanced to make sure the tablemap of the foreign table is loaded!
                    $relation = $propelColumn->getRelation();
                    if (!$relation->isComposite()) {
                        $map = $propelColumn->getRelatedTable()->getCoolValueMap();
                    }
                }
            }

            if ($map instanceof ValueMapInterface) {

                $vmapParameters = [
                    'schema' => $schemaName,
                    'table' => $tableName,
                    'column' => $columnName
                ];

                $map->setAjaxEndPoint( Cool::getInstance()->getFactory()->getRouter()->generate('columnVmap', $vmapParameters) );

                $additionalParameters = [];
                if($schema->isMultiTenant())
                    $additionalParameters['actualschema'] = $schema->getCurrentSchema();

                $map->getParameters()->add($additionalParameters);

                return $map;
            }
        } catch(\Exception $e) {
            // TODO: $columnName may be an extended field, which may have its valuemap defined somewhere in the extension itself
        }

        return false;
    }

    /**
     * @param $schemaName
     * @param $tableName
     * @return \Eulogix\Cool\Lib\DataSource\ValueMapInterface
     */
    public static function getValueMapForTable($schemaName, $tableName) {
        $map = null;

        try {
            $schema = Cool::getInstance()->getSchema($schemaName);
            $tableMap = $schema->getDictionary()->getPropelTableMap($tableName);
            return $tableMap->getCoolValueMap();
        } catch(\Exception $e) {

        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getValuesNumber() {
        return $this->getSchema()->fetch("SELECT COUNT(*) FROM {$this->getTableName()}");
    }

    /**
     * @inheritdoc
     */
    public function mapValue($value) {
        if(null===$value)
            return '-';

        $map = $this->getMap($value);
        try {
            return $map['label'] ?? null;
        } catch(\Exception $e) {
            return "({$value})!";
        }
    }

    /**
     * @inheritdoc
     */
    public function valueExists($value)
    {
        $map = $this->getMap($value);
        return isset($map['label']);
    }

    /**
     * The hard limit of 50 elements is there to prevent excessive computation time/memory
     * @inheritdoc
     */
    public function getMap($value = '', $searchText = "", $parameters = [], $limit = 50) {

        if(($searchText == "") && ($r = $this->getShim()->callMethod(__METHOD__, func_get_args()))) return $r;

        $emptyValue = $value === null || $value === '';
        $tMap = $this->getTableMap();
        $pk = $tMap->getPkFields()[0];

        $defaultLabelExpression = "'".$this->getTableName()."' || CAST($pk AS TEXT)";
        $labelExpression = ($customLabelExpression = $tMap->getCoolValueMapDecodingSQL()) ? $customLabelExpression : $defaultLabelExpression;

        if(!$emptyValue)
            $ret = $this->getSchema()->fetchArray("SELECT $pk AS value, CASE WHEN $labelExpression != '' THEN $labelExpression ELSE $defaultLabelExpression END AS label FROM {$this->getTableName()} WHERE $pk=:value", [':value'=>$value]);
        else {
            $sql = "SELECT $pk AS value, COALESCE($labelExpression,'') AS label FROM {$this->getTableName()}";

            if($this->allowedValues) {
                $sql.=" WHERE $pk IN (".implode(',',$this->allowedValues).")";
            } else $sql.=" WHERE TRUE ";

            $sqlParams = [];
            if($searchText) {
                $sqlParams[':query'] = "$searchText";
                $sqlParams[':rquery'] = "$searchText%";
                $sqlParams[':lquery'] = "%$searchText";
                $sqlParams[':lrquery'] = "%$searchText%";
                if($searchSQL = $tMap->getCoolValueMapSearchSQL())
                    $sql.= " AND ".$searchSQL;
                else $sql.= "AND ($labelExpression) ILIKE :lrquery";
            }

            $ret = $this->getSchema()->fetchArray($sql.($limit?" LIMIT $limit":""), !empty($sqlParams) ? $sqlParams : null, true);
        }

        //if the class overrides the getHumanDescription method, we use that to build the decodification string of the object
        $overridesHD = null;
        if(!$customLabelExpression) {
            foreach($ret as &$row) {
                if($overridesHD !== false) {
                    $obj = $this->getSchema()->getPropelObject($this->getTableName(), $row['value']);
                    if($overridesHD === null) {
                        $c = new \ReflectionClass($obj);
                        $hd = $c->getMethod('getHumanDescription');
                        $overridesHD = $hd->class !== 'Eulogix\Cool\Lib\Database\Propel\CoolPropelObject';
                    }
                    if($overridesHD)
                        $row['label'] = $obj->getHumanDescription();
                }
            }
        }

        return $emptyValue ? $ret : ($ret[0] ?? null);
    }

    /**
     * @inheritdoc
     */
    public function filterByAllowedValues($allowedValues)
    {
        $this->allowedValues = $allowedValues;
        return $this;
    }

    /**
     * @return string
     */
    protected function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return \Eulogix\Cool\Lib\Database\Schema
     */
    protected function getSchema() {
        return Cool::getInstance()->getSchema($this->schemaName);
    }

    /**
     * @return CoolTableMap
     */
    protected function getTableMap() {
        return  $tableMap = $this->getSchema()->getDictionary()->getPropelTableMap($this->getTableName());
    }
}