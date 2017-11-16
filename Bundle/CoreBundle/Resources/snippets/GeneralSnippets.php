<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Resources\snippets;

use Eulogix\Cool\Lib\Annotation\SnippetMeta;

class GeneralSnippets
{
    /**
     * @SnippetMeta(category="general_variable", contextIgnore={}, directInvocation="true", description="Get logged user ID")
     *
     * @return int
     */
    public static function getLoggedUserId()
    {
        return \Eulogix\Cool\Lib\Cool::getInstance()->getLoggedUser()->getId();
    }

    /**
     * @SnippetMeta(category="general_variable", contextIgnore={}, directInvocation="true", description="Get logged user LOGIN NAME")
     *
     * @return string
     */
    public static function getLoggedUserLoginName()
    {
        return \Eulogix\Cool\Lib\Cool::getInstance()->getLoggedUser()->getUsername();
    }

    /**
     * @SnippetMeta(category="general_variable", contextIgnore={}, directInvocation="true", description="Get logged user setting", longDescription="Returns the value of a setting for the logged user")
     *
     * @param string $settingName The name of the setting to retrieve
     *
     * @return string
     */
    public static function getLoggedUserSetting($settingName)
    {
        return \Eulogix\Cool\Lib\Cool::getInstance()->getLoggedUser()->getAccount()->getSetting($settingName);
    }

    /**
     * @SnippetMeta(category="general_variable", contextIgnore={}, directInvocation="true", description="Is logged user in group?", longDescription="returns TRUE if the user belongs to the group")
     *
     * @param string $groupId the numeric id of the group
     * @param string $groupName the name of the group (used if no id is provided)
     *
     * @return bool
     */
    public static function isLoggedUserInGroup($groupId, $groupName)
    {
        $cool = \Eulogix\Cool\Lib\Cool::getInstance();

        $group = null;

        if ($groupId) {
            $group = $cool->getFactory()->getGroupManager()->getGroup($groupId);
        } else {
            if ($groupName) {
                $group = $cool->getFactory()->getGroupManager()->getGroupByName($groupName);
            }
        }

        if ($group) {
            return $cool->getLoggedUser()->getAccount()->isInGroup($group);
        }

        return false;
    }

    /**
     * @SnippetMeta(category="general_variable", contextIgnore={}, directInvocation="true", description="Get CURRENT ISO8601 DATE")
     *
     * @return string
     */
    public static function getCurrentDate()
    {
        $d = new \DateTime();
        return $d->format('c');
    }

    /**
     * @SnippetMeta(category="general_variable", contextIgnore={}, directInvocation="true", description="Add days to a date")
     * @param string $startDate The start date
     * @param int $days the days to add
     * @return string
     */
    public static function dateAddDaysToADate($startDate, $days)
    {
        $d = new \DateTime($startDate);
        $d->add(new \DateInterval('P' . $days . 'D'));
        return $d->format('c');
    }

    /**
     * @SnippetMeta(category="general_action", contextIgnore={}, directInvocation="true", description="PENTAHO - run job")
     *
     * @param string $jobName The name of the job
     * @param string $jobPath (optional) The repository path of the job
     * @param string $JSONparameters JSON object with job parameters
     * @throws \Exception
     */
    public static function runPentahoJob($jobName, $jobPath, $JSONparameters)
    {
        $rd = \Eulogix\Cool\Lib\Cool::getInstance()->getFactory()->getRundeck();

        if ($jobId = $rd->getJobIdByName($coolJobName = 'cool:pentaho:runjob')) {
            $ret = $rd->runJob(
                $jobId,
                [
                    'job' => $jobName,
                    'job_path' => $jobPath,
                    'job_parameters_json' => $JSONparameters ?? '[]'
                ]
            );
        } else {
            throw new \Exception("Unable to find Rundeck job id for $coolJobName ");
        }
    }

    /**
     * @SnippetMeta(category="general_variable", contextIgnore={}, directInvocation="true", description="Get random string")
     *
     * @param string $length the length of the generated string. defaults to 10
     * @return string
     */
    public static function getRandomString($length)
    {
        $length = $length ?? 10;

        return substr(
            str_shuffle(
                str_repeat(
                    $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                    ceil($length / strlen($x))
                )
            ),
            1,
            $length
        );
    }

    /**
     * @SnippetMeta(category="general_action", contextIgnore={}, directInvocation="true", description="Push user notification")
     *
     * @param string $notificationContext (Optional) context string
     * @param string $message The message
     * @param string $jsonData optional json data object
     * @param string $userIds , separated list of user ids
     * @param string $jsOnClick JS that will be executed on click
     */
    public static function pushUserNotification($notificationContext, $message, $jsonData, $userIds, $jsOnClick)
    {
        $ids_ = explode(',', $userIds);

        if ($jsonData) {
            $decodedData = json_decode($jsonData, true);
        } else {
            $decodedData = [];
            if ($jsOnClick) {
                $decodedData[ 'jsOnClick' ] = $jsOnClick;
            }
        }

        $pm = \Eulogix\Cool\Lib\Cool::getInstance()->getFactory()->getPushManager();
        foreach ($ids_ as $userId_) {
            $pm->pushUserNotification(
                \Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserNotification::create(
                    $userId_,
                    $message,
                    $notificationContext,
                    $decodedData
                )
            );
        }
    }

    /**
     * @SnippetMeta(category="general_variable", contextIgnore={}, directInvocation="true", description="Fetch a value from the EXT field of a record")
     *
     * @param string $schemaName the schemaName
     * @param string $actualSchema the actualSchema
     * @param string $pk pk
     * @param string $fieldName fieldName
     * @param string $tableName The table name
     */
    public static function fetchAValueFromTheExtFieldOfARecord(
        $schemaName,
        $actualSchema,
        $pk,
        $fieldName,
        $tableName
    ) {
        $schema = \Eulogix\Cool\Lib\Cool::getInstance()->getSchema($schemaName);

        if ($actualSchema && $schema->isMultiTenant()) {
            $schema->setCurrentSchema($actualSchema);
        }

        $obj = $schema->getPropelObject($tableName, $pk);

        $ext = json_decode($obj->getExt() ?? '[]', true);

        return @$ext[ $fieldName ];
    }

    /**
     * @SnippetMeta(category="general_variable", contextIgnore={}, directInvocation="true", description="Get account ids in group", longDescription="Returns a comma separated list of accounts which belong to a group")
     *
     * @param string $groupId The ID of the group
     * @param string $groupName the NAME of the group (used if no id is provided)
     *
     * @return string
     */
    public static function getAccountIdsInGroup($groupId, $groupName)
    {
        $cool = \Eulogix\Cool\Lib\Cool::getInstance();

        $group = null;

        if ($groupId) {
            $group = $cool->getFactory()->getGroupManager()->getGroup($groupId);
        } else {
            if ($groupName) {
                $group = $cool->getFactory()->getGroupManager()->getGroupByName($groupName);
            }
        }

        if ($group) {
            return implode(',', $group->getAccountIds());
        }

        return '';
    }

    /**
     * @SnippetMeta(category="general_variable", contextIgnore={}, directInvocation="true", description="Get group ID from group name", longDescription="returns de group ID")
     *
     * @param string $groupName Group name
     *
     * @return int
     */
    public static function getGroupIdFromGroupName($groupName)
    {
        $cool = \Eulogix\Cool\Lib\Cool::getInstance();

        $group = $cool->getFactory()->getGroupManager()->getGroupByName($groupName);

        if ($group) {
            return $group->getAccountGroupId();
        }

        return '';
    }

    /**
     * @SnippetMeta(category="general_variable", contextIgnore={}, directInvocation="true", description="Workflows - determine if a process instance has a pending task for current user", longDescription="Returns BOOL if the process instance has a pending (assigned to or claimable) task for the logged user")
     *
     * @param string $processInstanceId The numeric ID of the process instance, or execution ID
     *
     * @return bool
     */
    public static function processInstanceHasAPendingTaskForCurrentUser($processInstanceId)
    {
        $wfEngine = \Eulogix\Cool\Lib\Cool::getInstance()->getFactory()->getWorkflowEngine();

        try {

            $process = $wfEngine->getClient()->getProcessInstance($processInstanceId);

            $pi = new \Eulogix\Lib\Activiti\om\ProcessInstance($process, $wfEngine->getClient());

            $pendingTask = $wfEngine->getFirstPendingTaskForUser($pi);

            return $pendingTask !== null;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @SnippetMeta(category="general_action", contextIgnore={}, directInvocation="true", description="Create / modify database record")
     *
     * @param string $schemaName The name of the schema
     * @param string $actualSchema (optional) the physical name of the schema, for multi tenant schemas
     * @param string $tableName the name of the table
     * @param string $pk (optional) the primary key, if left blank a new record will be created
     * @param string $jsonFields (optional) JSON object containing the fields and values to be inserted/updated
     * @param string $EXTJsonFields (optional) JSON object containing the fields and values to be inserted/updated in the EXT JSON container
     *
     * @return string
     */
    public static function createOrModifyDatabaseRecord(
        $schemaName,
        $actualSchema,
        $tableName,
        $pk,
        $jsonFields,
        $EXTJsonFields
    ) {
        $schema = \Eulogix\Cool\Lib\Cool::getInstance()->getSchema($schemaName);

        if ($actualSchema && $schema->isMultiTenant()) {
            $schema->setCurrentSchema($actualSchema);
        }

        $obj = $schema->getPropelObject($tableName, @$pk);

        if ($jsonFields) {
            $obj->fromJSON($jsonFields);
        }

        if ($EXTJsonFields) {
            $obj->setExt(
                json_encode(
                    array_merge(
                        json_decode($obj->getExt() ?? '[]', true),
                        json_decode($EXTJsonFields, true)
                    )
                )
            );
        }

        $obj->save();

        return $obj->getPrimaryKey();
    }
}