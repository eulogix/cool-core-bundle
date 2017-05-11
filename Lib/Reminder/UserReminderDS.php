<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Reminder;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserReminder;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserReminderQuery;
use Eulogix\Cool\Lib\Cool;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class UserReminderDS extends BaseReminderDS
{
    const USER_ID_PLACEHOLDER = "[_user_id_]";

    /**
     * @var UserReminder
     */
    protected $userReminder;

    /**
     * @param int $reminderDefinitionId
     * @throws \Exception
     */
    public function __construct($reminderDefinitionId)
    {

        if (!$userReminder = UserReminderQuery::create()->findPk($reminderDefinitionId)) {
            throw new \Exception("Can't find reminder with definition $reminderDefinitionId");
        }

        if (!$schema = Cool::getInstance()->getSchema($schemaName = $userReminder->getContextSchema())) {
            throw new \Exception("Bad schema defined in reminder definition $reminderDefinitionId: $schemaName");
        }

        $this->userReminder = $userReminder;

        parent::__construct($schemaName);

        if ($schema->isMultiTenant()) {
            $this->setUnioned(true);
        }

        $tables = explode(',',$userReminder->getParentTables());
        foreach($tables as $table)
            $this->addFields($this->getCoolSchema()->getDSFieldsFor( $table ));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFullSelectSql($parameters = array(), $query = null)
    {
        $user = Cool::getInstance()->getLoggedUser();

        $where = $this->getSqlWhere($parameters, $query);

        $select = str_replace(self::USER_ID_PLACEHOLDER, $user->getId(), $this->userReminder->getSqlQuery()); ;

        $mtPlaceHolder = self::MULTITENANT_SCHEMA_PLACEHOLDER;
        $schemaIdentifier = self::SCHEMA_IDENTIFIER;

        $select = "SELECT *,'$mtPlaceHolder' AS $schemaIdentifier FROM($select) AS _itmp ".$where['statement'];

        if ($this->isUnioned()) {
            $sql = "SELECT * FROM (\n";

            $unions = [];
            foreach ($this->getSchemaList() as $actualSchema) {
                $unions[] = str_replace(self::MULTITENANT_SCHEMA_PLACEHOLDER, $actualSchema, $select);
            }

            $sql .= implode("\n UNION ALL \n", $unions) . ")\n AS merge_alias";

        } else {
            $sql = str_replace(self::MULTITENANT_SCHEMA_PLACEHOLDER . '.', '', $select);
            $sql = str_replace(self::MULTITENANT_SCHEMA_PLACEHOLDER, '', $sql);
        }

        $ret = [
            'statement' => $sql,
            'parameters' => $where['parameters']
        ];

        return $ret;
    }

}