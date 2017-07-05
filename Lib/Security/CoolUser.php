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

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Account;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountQuery;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Lib\Database\Postgres\PgUtils;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CoolUser implements UserInterface {

    const ROLE_APP_USER = "ROLE_APP_USER";
    const ROLE_ADMIN = "ROLE_ADMIN";
    const ROLE_SUPER_ADMIN = "ROLE_SUPER_ADMIN";
    const ROLE_ALLOWED_TO_SWITCH = "ROLE_ALLOWED_TO_SWITCH";

    /**
     * @var Account
     */
    private  $Account;

    public function __construct(Account $Account) {
        $this->Account = $Account;
        //TODO: this could be moved to a listener or something like that
        //retrieve these values with SELECT current_setting('cool.user.loginName');
        Cool::getInstance()->getCoreSchema()->query("set session \"cool.user.id\" = '" .$Account->getAccountId()."';");
        Cool::getInstance()->getCoreSchema()->query("set session \"cool.user.loginName\" = '" .$Account->getLoginName()."';");
        Cool::getInstance()->getCoreSchema()->query("set session \"cool.user.firstName\" = '" .$Account->getFirstName()."';");
        Cool::getInstance()->getCoreSchema()->query("set session \"cool.user.lastName\" = '" .$Account->getLastName()."';");
    }

    /**
     * @param integer $id
     * @return $this
     */
    public static function fromId($id) {
        if($Account = AccountQuery::create()->findPk($id)) {
            return new static($Account);
        }
        return false;
    }

    /**
     * @return Account
     */
    public function getAccount() {
        return $this->Account;
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return PgUtils::fromPGArray($this->Account->getRoles());
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->Account->getPassword();
    }

    /**
     * @inheritdoc
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->Account->getLoginName();
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {

    }

    /**
     * @param $settingName
     * @return mixed,...
     */
    public function getSetting($settingName)
    {
        $replacedSetting = $settingName;
        if(func_num_args()>1) {
            for($i=1; $i<func_num_args(); $i++) {
                $replacedSetting = preg_replace('/(\{'.$i.'\})/im', func_get_arg($i), $replacedSetting);
            }
        }
        return $this->Account->getSetting($replacedSetting);
    }

    public function hasRole($role)
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->Account->getAccountId();
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->Account->getFirstName();
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->Account->getLastName();
    }

    /**
     * @return boolean
     */
    public function isAdmin() {
        return $this->hasRole( self::ROLE_ADMIN );
    }

}