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
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;
use Eulogix\Cool\Lib\Database\Propel\CoolTableMap;
use Eulogix\Cool\Lib\DataSource\DSRequest;
use Eulogix\Cool\Lib\DataSource\DSResponse;

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
        return $this->buildFullSelectSql($parameters, $query);
    }

    /**
     * returns a SELECT which only contains one fake field, used to COUNT more efficiently
     *
     * @param mixed $parameters
     * @param null $query
     * @return array
     */
    public function getStrippedCountSelectSql($parameters = array(), $query=null)
    {
        return $this->buildFullSelectSql($parameters, $query, true);
    }

    /**
     * @param array $parameters
     * @param string $query
     * @param bool $stripSelect
     * @return array
     */
    private function buildFullSelectSql($parameters = array(), $query = null, $stripSelect = false)
    {
        $user = Cool::getInstance()->getLoggedUser();

        $where = $this->getSqlWhere($parameters, $query);

        $select = str_replace(self::USER_ID_PLACEHOLDER, $user->getId(), $this->userReminder->getSqlQuery()); ;

        /**
         * replaces the SELECT portion of the query with a simple fake field for counting. If _date is found, it is
         * kept as this drives the dated matrix
         */
        if($stripSelect && preg_match('/select[ \t\r\n]*(.+?)(?!.*?[ \t\r\n]from )/sim', $select, $matches)) {
            $selectFieldsPortion = $matches[1];
            if(preg_match('/(,|)([^,]+? as _date)[, \r\n]/sim', $selectFieldsPortion, $dateExpressionMatches))
                $select = str_replace($selectFieldsPortion, $dateExpressionMatches[2].' ', $select);
            else $select = str_replace($selectFieldsPortion, ' 0 as fake ', $select);
        }

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

        if($recordId = @$parameters[self::RECORD_IDENTIFIER]) {
            @$ret[ 'statement' ] .= " WHERE (".self::RECORD_IDENTIFIER." = :_recordid) ";
            @$ret[ 'parameters' ][ ':_recordid' ] = $recordId;

        }

        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function executeUpdate(DSRequest $req) {
        $success = true;
        $dsresponse = new DSResponse($this);
        if($db = $this->getCoolSchema()) {

            $parentTables = explode(',',$this->userReminder->getParentTables());
            $parentTableMaps = [];
            foreach($parentTables as $parentTable) {
                $parentTableMaps[$parentTable] = $db->getDictionary()->getPropelTableMap($parentTable);
            }

            $connection = $db->getConnection();
            $fillData = $req->getValues();
            $recordId = $req->getParameters()[ self::RECORD_IDENTIFIER ];

            $connection->beginTransaction();
            try {
                if ($this->isUnioned() && ($hashSchema = @$fillData[ self::SCHEMA_IDENTIFIER ]) && $db->isSchemaNameValid($hashSchema)) {
                    $db->setCurrentSchema($hashSchema);
                }

                /**
                 * @var CoolPropelObject[] $propelObjects
                 */
                $propelObjects = [];
                foreach($parentTableMaps as $parentTableName => $tm) {
                    /**
                     * @var CoolTableMap $tm
                     */
                    $pkFields = $tm->getPkFields();
                    if(count($pkFields)==1 && isset($fillData[$pkFields[0]])) {
                        $propelObjects[$parentTableName] = $db->getPropelObject($parentTableName, $fillData[$pkFields[0]]);
                    }
                }

                $errors = $this->validate($fillData);
                if (!$errors->hasErrors()) {

                    $oldRecord = $this->getDSRecord($recordId, $req->getParameters());
                    $oldValues = $oldRecord->getValues();
                    foreach($oldValues as $fieldName => $oldValue) {
                        if(isset($fillData[$fieldName]) && $fillData[$fieldName] != $oldValue) {
                            foreach($propelObjects as $obj)
                                if($obj->getCoolTableMap()->hasColumn($fieldName))
                                    $obj->setByName($fieldName, $fillData[$fieldName], \BasePeer::TYPE_FIELDNAME);
                        }
                    }

                    foreach($propelObjects as $obj)
                        if($obj->isModified())
                            $obj->save();


                }
                $connection->commit();
            } catch (\Exception $e) {
                $dsresponse->getErrorReport()->addGeneralError("EXCEPTION");
                $dsresponse->getErrorReport()->addGeneralError($e->getMessage());
                $dsresponse->setData([]);

                $connection->rollback();
                $success = false;
            }

            $dsresponse->setAttribute(self::RECORD_IDENTIFIER, $recordId);
            $newRecord = $this->getDSRecord($recordId, $req->getParameters());
            $finalHash = $newRecord->getValues();

            if($req->getIncludeDecodings()) {
                $finalHash = $this->addDecodedValuesToHash($finalHash);
            }

            $dsresponse->setData( $finalHash );
        }
        $dsresponse->setStatus($success);
        return $dsresponse;
    }

}