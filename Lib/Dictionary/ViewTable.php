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

use Eulogix\Cool\Lib\Database\Propel\CoolTableMap;
use Eulogix\Cool\Lib\DataSource\Bean;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ViewTable extends Bean {

    /**
     * @var string
     */
    private $name, $join, $alias, $duplicatePrefix;

    /**
     * @var View
     */
    private $view;

    /**
     * @param View $view
     */
    public function __construct(View $view) {
        $this->view = $view;
    }

    /**
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return Dictionary
     */
    public function getDictionary()
    {
        return $this->getView()->getDictionary();
    }

    /**
     * @param string $alias
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $join
     * @return $this
     */
    public function setJoin($join)
    {
        $this->join = $join;
        return $this;
    }

    /**
     * @return string
     */
    public function getJoin()
    {
        return $this->join;
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
     * @return string
     */
    public function getDuplicatePrefix()
    {
        return $this->duplicatePrefix ? $this->duplicatePrefix : $this->getName();
    }

    /**
     * @param string $duplicatePrefix
     * @return $this
     */
    public function setDuplicatePrefix($duplicatePrefix)
    {
        $this->duplicatePrefix = $duplicatePrefix;
        return $this;
    }

    /**
     * returns the runtime propel tablemap for the given table
     *
     * @return CoolTableMap
     */
    public function getPropelTableMap() {
        return $this->getDictionary()->getPropelTableMap($this->getName());
    }

} 