<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Database\Propel\generator\builder;

use Eulogix\Cool\Lib\Database\Propel\generator\model\PropelBuilderTableProxy;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class PHP5TableMapBuilder extends \PHP5TableMapBuilder {

    protected function addClassOpen(&$script)
    {
        parent::addClassOpen($script);
        $script = str_replace(" extends TableMap", " extends \\Eulogix\\Cool\\Lib\\Database\\Propel\\CoolTableMap", $script);
    }

    /**
     * avoid model bloat by limiting fk methods and boilerplate in the core model
     *
     * @return PropelBuilderTableProxy
     */
    public function getTable()
    {
        $parentTable = parent::getTable();
        if($parentTable instanceof PropelBuilderTableProxy)
            return $parentTable;
        return new PropelBuilderTableProxy( $parentTable );
    }

} 