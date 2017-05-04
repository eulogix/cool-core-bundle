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
use \ForeignKey;

/**
 * Class AuditableBehavior
 *
 * This Behavior adds some auditing fields directly in the target table, that's handy for quick querying about who and
 * when created/updated a certain record without having to query the full fledged auditing schema, which may even be
 * unavailable for the table.
 *
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class AuditableBehavior extends \Behavior
{

    // default parameters value
    protected $parameters = array(
        'create_column'          => 'creation_date',
        'created_by_column'      => 'creation_user_id',
        'update_column'          => 'update_date',
        'updated_by_column'      => 'update_user_id',
        'version_column'         => 'record_version',
    );

    /**
     * Add the create_column and update_columns to the current table
     */
    public function modifyTable()
    {
        if (!$this->getTable()->hasColumn($this->getParameter('create_column'))) {
            $this->getTable()->addColumn(array(
                'name' => $this->getParameter('create_column'),
                'type' => 'TIMESTAMP'
            ));
        }

        if (!$this->getTable()->hasColumn($this->getParameter('update_column'))) {
            $this->getTable()->addColumn(array(
                'name' => $this->getParameter('update_column'),
                'type' => 'TIMESTAMP'
            ));
        }

        if (!$this->getTable()->hasColumn($this->getParameter('created_by_column'))) {
            $this->getTable()->addColumn(array(
                'name' => $this->getParameter('created_by_column'),
                'type' => 'INTEGER'
            ));

            $fk = new ForeignKey();
            $fk->setForeignTableCommonName('account');
            $fk->setForeignSchemaName('core');
            $fk->setOnDelete('RESTRICT');
            $fk->setOnUpdate(null);
            $fk->addReference($this->getParameter('created_by_column'), "account_id");
            $this->getTable()->addForeignKey($fk);
        }

        if (!$this->getTable()->hasColumn($this->getParameter('updated_by_column'))) {
            $this->getTable()->addColumn(array(
                'name' => $this->getParameter('updated_by_column'),
                'type' => 'INTEGER'
            ));

            $fk = new ForeignKey();
            $fk->setForeignTableCommonName('account');
            $fk->setForeignSchemaName('core');
            $fk->setOnDelete('RESTRICT');
            $fk->setOnUpdate(null);
            $fk->addReference($this->getParameter('updated_by_column'), "account_id");
            $this->getTable()->addForeignKey($fk);
        }

        if (!$this->getTable()->hasColumn($this->getParameter('version_column'))) {
            $this->getTable()->addColumn(array(
                'name' => $this->getParameter('version_column'),
                'type' => 'INTEGER'
            ));
        }

        $currentUid = Cool::getInstance()->getExecutionGuid();
        $fileId = "/* file generation UUID: $currentUid */\n";

        /**
         * hack that looks when this function is called in the context of Propel SQL build task.
         * This way we can create sql files in cool directories ensuring we do it only once
         */
        if(backtrace_search('PropelSqlBuildTask')) {
            $dir = $this->getTargetDir();
            $preSyncFileName = $dir.'/pre_sync/100_auto_auditable_behavior.sql';
            $postSyncFileName = $dir.'/post_sync/100_auto_auditable_behavior.sql';

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
            file_put_contents($postSyncFileName, $this->getPostSyncSQL() , FILE_APPEND);
        }
    }

    public function tableMapFilter(&$script)
    {

    }

    /**
     * @return string
     */
    private function getPreSyncSQL() {

        $tableName = $this->getTable()->getCommonName();
        $functionName = $tableName.'_audf';

        return "
--
-- Remove Auditing triggers for $tableName
--

DROP FUNCTION if EXISTS $functionName() CASCADE;\n\n

";
    }

    /**
     * @return string
     */
    private function getPostSyncSQL() {

        $tableName = $this->getTable()->getCommonName();
        $functionName = $tableName.'_audf';
        $triggerName = $functionName.'_trg';

        return "
--
-- Auditing triggers for $tableName
--

CREATE OR REPLACE FUNCTION $functionName() RETURNS TRIGGER AS
\$functionBlock\$
    BEGIN
        IF (TG_OP = 'UPDATE') THEN
            NEW.{$this->getParameter('version_column')} = COALESCE(NEW.{$this->getParameter('version_column')},1)+1;
            NEW.{$this->getParameter('update_column')} = NOW();
            NEW.{$this->getParameter('updated_by_column')} = core.get_logged_user();
        ELSIF (TG_OP = 'INSERT') THEN
            NEW.{$this->getParameter('version_column')} = 1;
            NEW.{$this->getParameter('create_column')} = COALESCE( NEW.{$this->getParameter('create_column')}, NOW() );
            NEW.{$this->getParameter('created_by_column')} = COALESCE( NEW.{$this->getParameter('created_by_column')}, core.get_logged_user() );
        END IF;
        RETURN NEW;
    END;
\$functionBlock\$
LANGUAGE plpgsql;

CREATE TRIGGER $triggerName BEFORE INSERT OR UPDATE ON $tableName
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
     * Add object attributes to the built class.
     *
     * @param \PHP5ObjectBuilder $builder
     *
     * @return string The PHP code to be added to the builder.
     */
    public function objectAttributes(\PHP5ObjectBuilder $builder)
    {
        $builder->declareClass("Eulogix\\Cool\\Lib\\Cool");
    }


    /**
     * Get the setter of one of the columns of the behavior
     *
     * @param string $column One of the behavior columns, 'create_column' or 'update_column'
     *
     * @return string The related setter, 'setCreatedOn' or 'setUpdatedOn'
     */
    protected function getColumnSetter($column)
    {
        return 'set' . $this->getColumnForParameter($column)->getPhpName();
    }

    /**
     * Return the constant for a given column.
     *
     * @param string    $columnName
     * @param \OMBuilder $builder
     *
     * @return string
     */
    protected function getColumnConstant($columnName, \OMBuilder $builder)
    {
        return $builder->getColumnConstant($this->getColumnForParameter($columnName));
    }


    /**
     * @param \QueryBuilder $builder
     * @return string
     */
    public function queryMethods(\QueryBuilder $builder)
    {
        $script = '';

        $queryClassName = $builder->getStubQueryBuilder()->getClassname();
        $createColumnConstant = $this->getColumnConstant('create_column', $builder);

            $updateColumnConstant = $this->getColumnConstant('update_column', $builder);

            $script .= "
/**
 * Filter by the latest updated
 *
 * @param      int \$nbDays Maximum age of the latest update in days
 *
 * @return     $queryClassName The current query, for fluid interface
 */
public function recentlyUpdated(\$nbDays = 7)
{
    return \$this->addUsingAlias($updateColumnConstant, time() - \$nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
}

/**
 * Order by update date desc
 *
 * @return     $queryClassName The current query, for fluid interface
 */
public function lastUpdatedFirst()
{
    return \$this->addDescendingOrderByColumn($updateColumnConstant);
}

/**
 * Order by update date asc
 *
 * @return     $queryClassName The current query, for fluid interface
 */
public function firstUpdatedFirst()
{
    return \$this->addAscendingOrderByColumn($updateColumnConstant);
}
";

        $script .= "
/**
 * Filter by the latest created
 *
 * @param      int \$nbDays Maximum age of in days
 *
 * @return     $queryClassName The current query, for fluid interface
 */
public function recentlyCreated(\$nbDays = 7)
{
    return \$this->addUsingAlias($createColumnConstant, time() - \$nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
}

/**
 * Order by create date desc
 *
 * @return     $queryClassName The current query, for fluid interface
 */
public function lastCreatedFirst()
{
    return \$this->addDescendingOrderByColumn($createColumnConstant);
}

/**
 * Order by create date asc
 *
 * @return     $queryClassName The current query, for fluid interface
 */
public function firstCreatedFirst()
{
    return \$this->addAscendingOrderByColumn($createColumnConstant);
}";

        return $script;
    }

}