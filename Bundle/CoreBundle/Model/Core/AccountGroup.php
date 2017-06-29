<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\om\BaseAccountGroup;

class AccountGroup extends BaseAccountGroup
{
    /**
     * returns the Accounts linked to this group, but only those which type is $type
     * @param string $type
     * @return Account[]
     */
    public function getUsersByType($type) {
        $ret = [];
        $c = new \Criteria();
        foreach( $this->getAccountGroupRefsJoinAccount($c->add(AccountPeer::TYPE, $type)) as $obj)
            $ret[] = $obj->getAccount();
        return $ret;
    }

    /**
     * @param Account $account
     * @return bool
     */
    public function containsAccount(Account $account) {
        return AccountGroupRefQuery::create()->filterByAccountGroup($this)->filterByAccount($account)->count() > 0;
    }

    /**
     * @return array
     */
    public function getAccountIds() {
        return $this->getCoolDatabase()->fetchArrayWithNumericKeys("SELECT account_id FROM account_group_ref WHERE account_group_id=:id", [':id' => $this->getPrimaryKey()]);
    }
}
