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
     * and referrers that point to a cool bundle schema if they are not themselves in a cool bundle
     */
    public function getReferrers()
    {
        $ret = [];
        $referrers = $this->propelTable->getReferrers();
        foreach($referrers as $referrer) {

            $tableNs = $this->propelTable->getNamespace();
            $refTableNs = $referrer->getTable()->getNamespace();

            if(
                !($this->nsBelongsToCoreBundle($tableNs) && !$this->nsBelongsToCoreBundle($refTableNs)) &&
                !($this->nsBelongsToCoolBundle($tableNs) && !$this->nsBelongsToCoolBundle($refTableNs))
            )
                $ret[] = $referrer;

        }
        return $ret;
    }

    /**
     * @param string $ns
     * @return int
     */
    private function nsBelongsToCoolBundle($ns) {
        return preg_match('/^Eulogix\\\\Cool\\\\.+?/sim', $ns) && !$this->nsBelongsToCoreBundle($ns);
    }

    /**
     * @param string $ns
     * @return int
     */
    private function nsBelongsToCoreBundle($ns) {
        return preg_match('/^Eulogix\\\\Cool\\\\Bundle\\\\CoreBundle\\\\.+?/sim', $ns);
    }
}