<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Dictionary;

use Eulogix\Cool\Lib\DataSource\Bean;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class View extends Bean {

    /**
     * @var string
     */
    private $name, $where;

    /**
     * @var Dictionary
     */
    private $dictionary;

    /**
     * @param Dictionary $dictionary
     */
    public function __construct(Dictionary $dictionary) {
        $this->dictionary = $dictionary;
    }

    /**
     * @return Dictionary
     */
    public function getDictionary()
    {
        return $this->dictionary;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $where
     * @return $this
     */
    public function setWhere($where)
    {
        $this->where = $where;
        return $this;
    }

    /**
     * @return string
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * @return ViewTable[]
     */
    public function getTables() {
        $ret = [];
        if($tbls = @$this->getDictionary()->getViewSettings($this->getName())[Dictionary::VIEW_ATT_TABLES]) {
            foreach($tbls as $t) {
                $vt = new ViewTable($this);
                $vt->populate($t);
                $ret[] = $vt;
            }
        }
        return $ret;
    }

} 