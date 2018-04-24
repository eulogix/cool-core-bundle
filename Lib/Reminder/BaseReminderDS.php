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

use Eulogix\Cool\Lib\DataSource\CoolCrudDataSource;
use Eulogix\Cool\Lib\DataSource\DSRequest;
use Eulogix\Cool\Lib\DataSource\DSResponse;
use Eulogix\Cool\Lib\Reminder\DSReminderProvider as Provider;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class BaseReminderDS extends CoolCrudDataSource {

    /**
     * @param array $parameters
     * @return bool|string
     */
    protected function getDateWhereExpression($parameters){
        if(isset($parameters[Provider::COMPARISON])) {
            $dateExpression = $this->getSQLDateExpression();
            $dateTime = $parameters[Provider::ISODATE];

            //emits an exception if $dateTime is not a suitable format
            $dt = new \DateTime($dateTime);

            switch($parameters[Provider::COMPARISON]) {
                case Provider::COMPARISON_GREATER: {
                    $operator = '>';
                    break;
                }
                case Provider::COMPARISON_SMALLER: {
                    $operator = '<';
                    break;
                }
                default: $operator = '=';
            }
            return "({$dateExpression} {$operator} '{$dateTime}')";
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getSqlWhere( $parameters = array(), $query=null ) {
        $ret = parent::getSqlWhere($parameters, $query);
        if($expr = $this->getDateWhereExpression($parameters)) {
            $ret['statement'].=" AND ($expr)";
        }
        return $ret;
    }

    /**
     * @return string
     */
    protected function getSQLDateExpression()
    {
        return '_date::date';
    }

    /**
     * @inheritdoc
     */
    public function _sqlExpression($fieldName) {
        return $fieldName;
    }

    /**
     * used to differentiate querying strategy if the request comes from a lister
     * instead of the base counting matrix.
     * The reason is that requests coming from a detail lister may contain parameters
     * that render the query stripping impractical
     *
     * @param array $parameters
     * @return bool
     */
    public function isRequestComingFromALister(array $parameters) {
        return isset($parameters['provider']);
    }

    /**
     * @inheritdoc
     */
    public function execute(DSRequest $req) {
        if($this->isUnioned()) {
            if ($schemas = json_decode(@$req->getParameters()[ self::SCHEMA_PARAM_IDENTIFIER ])) {
                $this->setSchemaList($schemas);
            }
            if (count($this->getSchemaList()) == 0) {
                $dsresponse = new DSResponse($this);
                $dsresponse->setData([]);
                $dsresponse->setStartRow(0);
                $dsresponse->setEndRow(0);
                $dsresponse->setTotalRows(0);
                $dsresponse->setStatus(true);

                return $dsresponse;
            }
        }
        return parent::execute($req);
    }

}