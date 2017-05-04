<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Database\Propel\generator\model;

/**
 * Decorated class that avoids the generation of bloat in the OM builder
 *
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class PropelBuilderTableProxy
{
    /**
     * @var \Table
     */
    private $propelTable;

    public function __construct(\Table $table) {
        $this->propelTable = $table;
    }

    public function __call($method, $args)
    {
        $ret = call_user_func_array([$this->propelTable, $method], $args);
        return $ret;
    }

    /**
     * filters out all the referrers which point to a core schema entity if they belong to another schema
     */
    public function getReferrers()
    {
        $ret = [];
        $referrers = $this->propelTable->getReferrers();
        foreach($referrers as $referrer) {
            if(!($this->propelTable->getSchema() == 'core' && $referrer->getTable()->getSchema() != 'core'))
                $ret[] = $referrer;
        }
        return $ret;
    }
}