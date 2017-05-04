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

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Database\Schema;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Account;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountQuery;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class UserManager {
    
    /**
    * @var Schema
    */
    protected $schema;
    
    public function __construct() {
        $this->schema = Cool::getInstance()->getCoreSchema();
    }

    /**
    * @param int $id
    * @return Account
    */
    public function getUserById($id) {
        return AccountQuery::create()->findOneByAccountId($id);
    }    
    
    /**
    * @param mixed $loginName
    * @return Account
    */
    public function getUserByLoginName($loginName) {
        return AccountQuery::create()->findOneByLoginName($loginName);
    }

    /**
     * @return CoolUser
     */
    public static function getLoggedUser() {
        $securityToken = Cool::getInstance()->getContainer()->get('security.token_storage')->getToken();
        if($securityToken instanceof TokenInterface) {
            $tkUser = $securityToken->getUser();
            if($tkUser instanceof CoolUser)
                return $tkUser;
        }
        return self::getEmptyUser();
    }

    /**
     * returns the ids of all the Accounts still logged in the system
     * @return int[]
     */
    public static function getLoggedUserIds() {
        //TODO: implement properly
        return Cool::getInstance()->getCoreSchema()->fetchArrayWithNumericKeys("SELECT account_id FROM account ORDER BY account_id ASC limit 10");
    }

    /**
     * @return CoolUser
     */
    private static function getEmptyUser()
    {
        return new CoolUser(new Account());
    }

}