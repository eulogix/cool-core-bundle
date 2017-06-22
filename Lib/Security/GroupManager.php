<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Security;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountGroup;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountGroupQuery;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Database\Schema;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class GroupManager {
    
    /**
    * @var Schema
    */
    protected $schema;
    
    public function __construct() {
        $this->schema = Cool::getInstance()->getCoreSchema();
    }

    /**
     * @param $type
     * @return AccountGroup[]
     */
    public function getGroupsByType($type) {
        return AccountGroupQuery::create()->findByType($type);
    }

    /**
     * @param int $id
     * @return AccountGroup
     */
    public function getGroup($id) {
        return AccountGroupQuery::create()->findPk($id);
    }

    /**
     * @param string $name
     * @return AccountGroup
     */
    public function getGroupByName($name) {
        return AccountGroupQuery::create()->findOneByName($name);
    }

}