<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\DataSource\Classes\Audit;

use Eulogix\Cool\Lib\Audit\AuditSchema;
use Eulogix\Cool\Lib\DataSource\CoolCrudDataSource as CD;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class DSFieldAuditTrailDataSource extends CD {

    const AUDITED_FIELD = "audited_field";

    /**
     * @var CD
     */
    protected $sourceDS;

    /**
     * @var string
     */
    protected $driverFieldName, $tableAuditedField, $uid = null;

    public function __construct(CD $sourceDS, $driverFieldName=null, $parameters=[])
    {
        $this->setReadOnly(true);

        $auditSchema = $sourceDS->getCoolSchema()->getCurrentAuditSchemaName();

        $this->sourceDS = $sourceDS;
        $this->driverFieldName = $driverFieldName;
        $sourceRelations = $sourceDS->getTableRelations();
        $driverRelation = $sourceDS->getField($driverFieldName)->getSource();

        $trailDSParameters = [];

        if($sourceRecordId = $parameters[CD::RECORD_IDENTIFIER]) {
            $pks = $sourceDS->explodePks($sourceRecordId);
        } else $pks = null;

        foreach($sourceRelations as $rk => $r)
            if(!$r->isView() && $driverRelation === $r) {

                //add all the fields from the source relation
                $r ->setAlternateTable($auditSchema.'.'.$r->getRawTableName())
                   ->setDeleteFlag(false)
                   ->setSkipUpdateFlag(true)
                   ->setJoinCondition(null)
                   ->setAlias(null);

                $trailDSParameters[ CD::PARAM_TABLE_RELATIONS ][] = $r;

                if($pks) {
                    $trailDSParameters[ 'source_pk' ] = $pks[ $rk ];
                }

                $this->uid = $r->getSchema().'_'.$r->getRawTableName();
                $this->tableAuditedField = $r->getPrefix() ? preg_replace('/^'.$r->getPrefix().'/sim','',$driverFieldName) : $driverFieldName;
            }

        parent::__construct($sourceDS->getCoolSchema()->getName(), $trailDSParameters);

        AuditSchema::addFieldsToDs($this);
        $this->getField( AuditSchema::FIELD_EVENT_ID )->setIsPrimaryKey(true);
    }

    /**
     * @inheritdoc
     */
    public function build($parameters=[]) {
        parent::build($parameters);

        $driverFieldName = $this->driverFieldName;

        $af = $this->addField(self::AUDITED_FIELD)
            ->setType($this->getField($driverFieldName)->getType())
            ->setControlType($this->getField($driverFieldName)->getControlType());

        if($vm = $this->getField($driverFieldName)->getValueMap())
            $af->setValueMap($vm);

        return $this;
    }

    /**
     * @return string
     */
    public function getUid()
    {
        return strtoupper($this->uid);
    }

    /**
     * @inheritdoc
     */
    public function getSqlWhere($parameters = array(), $query=null) {
        if($sourcePk = $this->getParameter('source_pk'))
            $parameters[CD::RECORD_IDENTIFIER] = $sourcePk;

        $ret = parent::getSqlWhere($parameters, $query);

        if($this->driverFieldName) {
            $ret['statement'].=" AND (".AuditSchema::FIELD_ACTION." = 'I' OR exist(".AuditSchema::FIELD_CHANGED_FIELDS.",:_auditDriverField))";
            $ret['parameters'][':_auditDriverField'] = $this->tableAuditedField;
        }

        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function getSqlBaseColumnsSelect($parameters = []) {
        $sql = parent::getSqlBaseColumnsSelect($parameters);
        $sql.=",".implode(',',AuditSchema::getSQLExpressions());
        $sql.=",{$this->tableAuditedField} AS ".self::AUDITED_FIELD;
        return $sql;
    }

    /**
     * @inheritdoc
     */
    public function getSQLPKExpression() {
        return AuditSchema::FIELD_EVENT_ID;
    }

    /**
     * @inheritdoc
     */
    public function getShimUID() {
        $UID = $this->schemaName . get_class($this->sourceDS) . $this->driverFieldName . ($this->getCoolSchema()->isMultiTenant() ? $this->getCoolSchema()->getCurrentSchema() : "");
        return md5($UID);
    }

}
