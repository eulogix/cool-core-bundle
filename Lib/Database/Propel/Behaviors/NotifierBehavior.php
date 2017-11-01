<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Database\Propel\Behaviors;

use Eulogix\Cool\Lib\Cool;

/**
 * Class NotifierBehavior
 *
 * This Behavior adds a trigger that uses PG_NOTIFY to broadcast a notification when the data in the table is changed
 *
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class NotifierBehavior extends \Behavior
{

    protected $parameters = array(
        'channel' => null, // defaults to the c_<table name>
        'per_row' => false, // if set, the trigger will execute per row with a more detailed payload
    );

    public function modifyTable()
    {
        $currentUid = Cool::getInstance()->getExecutionGuid();
        $fileId = "/* file generation UUID: $currentUid */\n";

        /**
         * hack that looks when this function is called in the context of Propel SQL build task.
         * This way we can create sql files in cool directories ensuring we do it only once
         */
        if(backtrace_search('PropelSqlBuildTask')) {
            $dir = $this->getTargetDir();
            $preSyncFileName = $dir.'/pre_sync/110_auto_notifier_behavior.sql';
            $postSyncFileName = $dir.'/post_sync/110_auto_notifier_behavior.sql';

            /**
             * since this routine runs more than once, if the behavior is applied to more than one table,
             * this ensures that target files get created only once (per execution)
             */
            if(file_exists($postSyncFileName)) {
                $storedFileUid = file($postSyncFileName)[0];
                if($storedFileUid != $fileId) {
                    file_put_contents($preSyncFileName, $fileId);
                    file_put_contents($postSyncFileName, $fileId);
                }
            } else {
                file_put_contents($preSyncFileName, $fileId);
                file_put_contents($postSyncFileName, $fileId);
            }

            file_put_contents($preSyncFileName, $this->getPreSyncSQL() , FILE_APPEND);
            file_put_contents($postSyncFileName, $this->getParameter('per_row') ? $this->getPostSyncSQLPerRow() : $this->getPostSyncSQLPerStatement() , FILE_APPEND);
        }
    }

    /**
     * @return string
     */
    private function getPreSyncSQL() {

        $tableName = $this->getTable()->getCommonName();
        $functionName = $tableName.'_notf';

        return "
--
-- Remove Notofier triggers for $tableName
--

DROP FUNCTION if EXISTS $functionName() CASCADE;\n\n

";
    }

    /**
     * @return string
     */
    private function getPostSyncSQLPerStatement() {

        $schema = $this->getParameter('schema');
        $tableName = $this->getTable()->getCommonName();
        $functionName = $tableName.'_notf';
        $triggerName = $functionName.'_notf_trg';

        $channel = $this->cleanChannelName( $this->getParameter('channel') ?? 'c_'.$tableName );

        return "
--
-- Notifier triggers for $tableName
--

CREATE OR REPLACE FUNCTION $functionName() RETURNS TRIGGER AS
\$functionBlock\$
    BEGIN
        PERFORM pg_notify('$channel', '{ \"schema\": \"$schema\", \"actual_schema\": \"' || TG_TABLE_SCHEMA || '\", \"operation\": \"' || TG_OP || '\" }' );
        PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || '$channel',  '{ \"schema\": \"$schema\", \"actual_schema\": \"' || TG_TABLE_SCHEMA || '\", \"operation\": \"' || TG_OP || '\" }');
        RETURN NULL;
    END;
\$functionBlock\$
LANGUAGE plpgsql;

CREATE TRIGGER $triggerName AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON $tableName
    FOR EACH STATEMENT EXECUTE PROCEDURE $functionName();\n\n
";
    }

    /**
     * @return string
     */
    private function getPostSyncSQLPerRow() {

        $tableName = $this->getTable()->getCommonName();
        $functionName = $tableName.'_notf';
        $triggerName = $functionName.'_notf_trg';

        $schema = $this->getParameter('schema');
        $channel = $this->cleanChannelName( $this->getParameter('channel') ?? 'c_'.$tableName );
        $pk = $this->getTable()->getFirstPrimaryKeyColumn()->getName();

        return "
--
-- Notifier triggers for $tableName
--

CREATE OR REPLACE FUNCTION $functionName() RETURNS TRIGGER AS
\$functionBlock\$
    BEGIN
        IF (TG_OP = 'DELETE') THEN
            PERFORM pg_notify('$channel', '{ \"schema\": \"$schema\", \"actual_schema\": \"' || TG_TABLE_SCHEMA || '\", \"operation\": \"' || TG_OP || '\", \"pk\": \"' || OLD.$pk || '\" }' );
            PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || '$channel', '{ \"schema\": \"$schema\", \"actual_schema\": \"' || TG_TABLE_SCHEMA || '\", \"operation\": \"' || TG_OP || '\", \"pk\": \"' || OLD.$pk || '\" }');
        ELSE
            PERFORM pg_notify('$channel', '{ \"schema\": \"$schema\", \"actual_schema\": \"' || TG_TABLE_SCHEMA || '\", \"operation\": \"' || TG_OP || '\", \"pk\": \"' || NEW.$pk || '\" }');
            PERFORM pg_notify(TG_TABLE_SCHEMA || ';' || '$channel', '{ \"schema\": \"$schema\", \"actual_schema\": \"' || TG_TABLE_SCHEMA || '\", \"operation\": \"' || TG_OP || '\", \"pk\": \"' || NEW.$pk || '\" }');
        END IF;
        RETURN NULL;
    END;
\$functionBlock\$
LANGUAGE plpgsql;

CREATE TRIGGER $triggerName AFTER INSERT OR UPDATE OR DELETE ON $tableName
    FOR EACH ROW EXECUTE PROCEDURE $functionName();\n\n
";
    }

    /**
     * @return string
     */
    private function getTargetDir() {
        $target = explode('/', $this->getParameter('target'));
        $bundle = $target[0]; $database = $target[1];
        $loc = Cool::getInstance()->getFactory()->getFileLocator()->locate('@'.$bundle.'/Resources/databases/'.$database.'/sql');
        return $loc;
    }

    /**
     * @param string $channelName
     * @return string
     */
    public static function cleanChannelName($channelName) {
        if(strlen($channelName)>64)
            return substr($channelName, 0, 64);
        return $channelName;
    }
}