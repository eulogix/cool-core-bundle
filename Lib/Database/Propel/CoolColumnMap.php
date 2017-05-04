<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Database\Propel;

/**
 * this class exists only to fool IDEs: code completion and such
 *
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CoolColumnMap extends \ColumnMap {

    /**
     * @inheritdoc
     * @return CoolTableMap
     */
    public function getTable()
    {
        return parent::getTable();
    }

    /**
     * @inheritdoc
     * @return CoolTableMap
     */
    public function getRelatedTable()
    {
        return parent::getRelatedTable();
    }
} 