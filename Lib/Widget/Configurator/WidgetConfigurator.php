<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Widget\Configurator;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Schema;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Widget\WidgetInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class WidgetConfigurator implements WidgetConfiguratorInterface {

    /**
     * @var WidgetInterface
     */
    protected $widget;

    /**
     * @param WidgetInterface $widget
     */
    public function __construct($widget) {
        $this->widget = $widget;
    }

    /**
     * returns the name of the table that contains the configuration
     * @return string
     */
    abstract protected function getTable();

    /**
     * returns the unique key used to store and retrieve widget conf
     * @return string
     */
    abstract protected function getWidgetId();

    /**
     * @return Schema
     */
    protected function getCoolSchema() {
        return  Cool::getInstance()->getCoreSchema();
    }

    /**
     * @inheritdoc
     */
    public function getStoredVariations($variation=null) {
        $cdb = $this->getCoolSchema();
        $name = $this->getWidgetId();
        $table = $this->getTable();

        if(!$variation) {
            $variation = $this->widget->getVariation();
        }

        $tmp = [];
        $sqlParams = array(':name'=>$name);

        $sql = "SELECT variation FROM $table WHERE variation IS NOT NULL AND name=:name";
        if($variation) {
            foreach($variation as $level=>$value) {
                $tmp[$level] = $cdb->fetchArray($sql, $sqlParams);
                $sql.= " AND variation LIKE '%[$level:$value]%'";
            }
        }

        $ret = [];
        foreach($tmp as $level=>$rows) {
            if(is_array($rows)) {
                foreach($rows as $row) {
                    if(preg_match('/\['.$level.':(.+?)\]/sim', $row['variation'], $m)) {
                        @$ret[$level][$m[1]]++;
                    }
                }
            }
        }

        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function getStoredId($variation=null) {
        $cdb = $this->getCoolSchema();
        $name = $this->getWidgetId();
        $table = $this->getTable();

        $vs = '';

        if(!$variation) {
            $variation = $this->widget->getVariation();
        }
        $sql = "SELECT {$table}_id FROM $table WHERE name=:name";

        if($variation) {
            $vs = $this->getVariationString($variation);
            $sql.=" AND variation=:variation";
        } else $sql.=" AND variation IS NULL";

        $idb = $cdb->fetch($sql, array(':name'=>$name, ':variation'=>$vs), true);
        return $idb ? $idb : false;
    }

    /**
     * @inheritdoc
     */
    public function getBestMatchingStoredId($variation=null) {
        $cdb = $this->getCoolSchema();
        $name = $this->getWidgetId();
        $table = $this->getTable();

        if(!$variation) {
            $variation = $this->widget->getVariation();
        }
        $sql = "SELECT {$table}_id FROM $table WHERE name=:name";
        $sqlParams = array(':name'=>$name);
        if($variation) {
            foreach($variation as $level=>$value) {
                $levelExistsCond = " AND variation LIKE '%[$level:$value]%'";
                $defaultExistsCond = " AND variation LIKE '%[$level:default]%'";


                $nr = $cdb->countRows($sql.$levelExistsCond, $sqlParams);
                if($nr > 0) {
                    //if there are stored configs with $value at $level, we restrict the query to them
                    $sql = $sql.$levelExistsCond;
                } else {
                    $nr = $cdb->countRows($sql.$defaultExistsCond, $sqlParams);
                    if($nr > 0) {
                        //otherwise, we grab the "default" stored configs
                        $sql = $sql.$defaultExistsCond;
                    } else {
                        //for this level of variation, no specific nor default configs could be found
                        return false;
                    }
                }
            }
        } else $sql.=" AND variation IS NULL";

        $idb = $cdb->fetch($sql, $sqlParams);
        return $idb && !is_array($idb) ? $idb : false;
    }

    /**
     * @inheritdoc
     */
    public function getVariationString($variation=null) {
        if(!$variation) {
            $variation = $this->widget->getVariation();
        }
        $varr = [];
        foreach($variation as $cat=>$v) {
            $varr[] = "[$cat:$v]";
        }
        return implode('',$varr);
    }

} 