<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\DataSource;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class SqlDataSource extends BaseDataSource {
    
    /**
    * returns the SQL select portion
    * @param array $parameters
    * @return string
    */
    abstract public function getSqlSelect( $parameters = [] );

    /**
     * returns the SQL clause needed to fetch the summary (totals, averages..) from the current query
     *
     * @param string[] $summaryFields
     * @param array $parameters
     * @param mixed $query
     * @return mixed []
     */
    public function getSqlSummary($summaryFields, $parameters = [], $query=null) {
        $expressions = [];
        foreach($summaryFields as $fieldName => $summaryType) {
            $expressions[]=$this->getSummaryExpressionFor($fieldName, $summaryType);
        }
        $select = "SELECT ".implode(',', $expressions);

        $where = $this->getSqlWhere($parameters, $query);
        return array('statement'=> implode(' ',array(
                            $select,
                            $this->getSqlFrom($parameters),
                            $where['statement'])),
                     'parameters'=>array_merge(
                            $where['parameters']
                    )
        );
    }

    /**
     * returns the atomic expression for a summary
     * @param $fieldName
     * @param $expressionType
     * @return string
     */
    public function getSummaryExpressionFor($fieldName, $expressionType) {
        switch($expressionType) {
            case DSRequest::SUMMARY_TOTAL:
            default: {
                return "SUM($fieldName) AS $fieldName";
                break;
            }
        }
    }

    /**
    * returns the SQL from portion
    * @param mixed $parameters
    * @return string
    */
    abstract public function getSqlFrom( $parameters = array() );

    /**
     * returns the final SQL query that can be fed to the dbms to retrieve a chunk of rows. Comes complete of ORDER BY and LIMIT clauses
     *
     * @param mixed $start
     * @param mixed $end
     * @param mixed $sort
     * @param mixed $parameters
     * @param null $query
     * @return array
     */
    public function getSql($start, $end, $sort, $parameters = array(), $query=null) {
        $where = $this->getSqlWhere($parameters, $query);
        $ret = array('statement'=> implode(' ',array(
            $this->getSqlSelect($parameters),
            $this->getSqlFrom($parameters),
            $where['statement'],
            $this->getSqlGroupBy($parameters),
            $this->getSqlSort($sort),
            $this->getSqlLimit($start, $end))),

            'parameters'=>array_merge(
                $where['parameters']
            )
        );
        return $ret;
    }

    /**
    * returns the SQL group by portion
    * @param mixed $parameters
    * @return string
    */
    public function getSqlGroupBy( $parameters = array() ) {
        $expressions = $this->getGroupByExpressions();
        if(!empty($expressions))
            return "GROUP BY ".implode("\n,", $expressions);
        return "";
    }

    /**
     * returns the SQL group by expressions
     * @param mixed $parameters
     * @return string[]
     */
    protected function getGroupByExpressions( $parameters = array() ) {
        return [];
    }

    /**
     * basic implementation that checks for equality...look in coolLister for management of extended columns
     * TODO: refactor/improve
     *
     * @param string $columnName
     * @param string $paramName
     * @param $condition
     * @return string
     */
    protected function getClause($columnName, $paramName, $condition) {
        return "($columnName = $paramName)";
    }

    /**
     * the most basic filter forms provide input parameters like _filter_<fieldName>, which have to be checked by equality
     *
     * @param mixed $parameters
     * @return array|bool
     */
    protected function getFilterClause( $parameters = array() ) {
        $retClauses = [];
        $retParameters = [];

        if($parameters)
            foreach($parameters as $paramName => $paramValue) {
                if( !empty($paramValue)
                    && preg_match('/^_lstr_fltr_(.+?)$/sim', $paramName, $m)
                    && ($field = $this->getField( $m[1] )) ) {
                        //we have received a filter parameter that matches one of the lister columns
                        $retClauses[] = $this->getClause($m[1], ":$paramName", "EQUALS");
                        $retParameters[":$paramName"] = $paramValue;
                }
            }
        if($retClauses) {
            return array(
                'statement'     =>  '('.implode(' AND ', $retClauses).')',
                'parameters'    =>  $retParameters
            );
        }
        return false;
    }

    /**
     * returns the SQL where portion
     * @param mixed $parameters
     * @param null|array $query
     * @return array []  an array containing two elements: parameters and statement
     */
    public function getSqlWhere( $parameters = array(), $query=null ) {
        $retStatement = "WHERE TRUE";
        $retParameters = [];
        
        //merge the cool filter (deprecated)
        if( $filterClause = $this->getFilterClause( $parameters ) ) {
            $retStatement.= " AND ".$filterClause['statement'];
            $retParameters = $filterClause['parameters'];
        }

        //check if there are timeline parameters TODO: bit of a hack, refactor in a cleaner way
        if( isset($parameters['timelineStart']) ) {
            $field = $parameters['timelineDateField'];

            $retStatement.= " AND $field::timestamp <@ tsrange(:timelineStart, :timelineEnd)";

            $retParameters[':timelineStart'] = $parameters['timelineStart'];
            $retParameters[':timelineEnd'] = $parameters['timelineEnd'];
        }

        if($expr = $this->getQueryExpression($query)) {
            $retStatement.= " AND ($expr)";
        }
        
        return array(
                'statement'     => $retStatement,
                'parameters'    => $retParameters
            );   
    }

    /**
     * produces the ORDER BY part of the query
     *
     * @param mixed $sort The sort array in the format [columnName] => 'A' || 'D'
     * @return string
     */
    public function getSqlSort($sort) {
        $s = '';
        if(is_array($sort) && count($sort)>0) {
            $tokens = [];
            foreach($sort as $col => $direction) {
                $tokens[] = "$col ".($direction==DSRequest::SORT_ASC?'ASC':'DESC');
            }
            $s = 'ORDER BY '.implode(',',$tokens).' NULLS LAST';
        }  
        return $s;  
    }

    /**
     * prduces the LIMIT part of the query
     *
     * @param integer $start
     * @param integer $end
     * @return string
     */
    public function getSqlLimit($start, $end) {
        $s = '';
        if($start!==null && $end!==null) {
            $limit_to   = 1+$end-$start;
            $s="LIMIT $limit_to OFFSET $start";
        }
        return $s;  
    }

    protected function _sqlExpression($fieldName) {
        return $fieldName;
    }

    protected function _f_and() {
        return "(".implode(" AND ", func_get_args()).")";
    }

    protected function _f_or() {
        return "(".implode(" OR ", func_get_args()).")";
    }

    protected function _f_isEmpty($fieldName) {
        $sqlExpression = $this->_sqlExpression($fieldName);
        switch($this->getField($fieldName)->getMacroType()) {
            case DSField::MACRO_TYPE_STRING  : return "($sqlExpression='' OR $sqlExpression IS NULL)"; break;
            case DSField::MACRO_TYPE_NUMERIC : return "($sqlExpression=0 OR $sqlExpression IS NULL)"; break;
            case DSField::MACRO_TYPE_DATETIME :
            case DSField::MACRO_TYPE_BOOLEAN : return "($sqlExpression IS NULL)"; break;
        }
    }

    protected function _f_contain($fieldName, $arg) {
        $sqlExpression = $this->_sqlExpression($fieldName);
        switch($this->getField($fieldName)->getMacroType()) {
            case DSField::MACRO_TYPE_STRING  : return "($sqlExpression ILIKE '%$arg%')"; break;
            case DSField::MACRO_TYPE_NUMERIC : return "($sqlExpression::text ILIKE '%$arg%')"; break;
            default: return "(FALSE)"; break;
        }
    }

    protected function _f_startWith($fieldName, $arg) {
        $sqlExpression = $this->_sqlExpression($fieldName);
        switch($this->getField($fieldName)->getMacroType()) {
            case DSField::MACRO_TYPE_STRING  : return "($sqlExpression ILIKE '$arg%')"; break;
            case DSField::MACRO_TYPE_NUMERIC : return "($sqlExpression::text ILIKE '$arg%')"; break;
            default: return "(FALSE)"; break;
        }
    }

    protected function _f_endWith($fieldName, $arg) {
        $sqlExpression = $this->_sqlExpression($fieldName);
        switch($this->getField($fieldName)->getMacroType()) {
            case DSField::MACRO_TYPE_STRING  : return "($sqlExpression ILIKE '%$arg')"; break;
            case DSField::MACRO_TYPE_NUMERIC : return "($sqlExpression::text ILIKE '%$arg')"; break;
            default: return "(FALSE)"; break;
        }
    }

    protected function _f_equal($fieldName, $arg) {
        return $this->_f_general_('=', $fieldName, $arg);
    }

    protected function _f_different($fieldName, $arg) {
        return $this->_f_general_('!=', $fieldName, $arg);
    }

    protected function _f_less($fieldName, $arg) {
        return $this->_f_general_('<', $fieldName, $arg);
    }

    protected function _f_lessEqual($fieldName, $arg) {
        return $this->_f_general_('<=', $fieldName, $arg);
    }

    protected function _f_greater($fieldName, $arg) {
        return $this->_f_general_('>', $fieldName, $arg);
    }

    protected function _f_greaterEqual($fieldName, $arg) {
        return $this->_f_general_('>=', $fieldName, $arg);
    }

    protected function _f_between($fieldName, $arg, $arg2) {
        $sqlExpression = $this->_sqlExpression($fieldName);
        switch($this->getField($fieldName)->getMacroType()) {
            case DSField::MACRO_TYPE_DATETIME  :
            case DSField::MACRO_TYPE_NUMERIC : return "($sqlExpression >= $arg AND $sqlExpression <= $arg2)"; break;
        }
    }

    protected function _f_outside($fieldName, $arg, $arg2) {
        $sqlExpression = $this->_sqlExpression($fieldName);
        switch($this->getField($fieldName)->getMacroType()) {
            case DSField::MACRO_TYPE_DATETIME  :
            case DSField::MACRO_TYPE_NUMERIC : return "($sqlExpression < $arg OR $sqlExpression > $arg2)"; break;
        }
    }

    private function _f_general_($operator, $fieldName, $arg) {
        $sqlExpression = $this->_sqlExpression($fieldName);
        switch($this->getField($fieldName)->getMacroType()) {
            case DSField::MACRO_TYPE_DATETIME  :
            case DSField::MACRO_TYPE_STRING  : return "($sqlExpression $operator '$arg')"; break;
            case DSField::MACRO_TYPE_NUMERIC : return "($sqlExpression $operator $arg)"; break;
            case DSField::MACRO_TYPE_BOOLEAN : return "($sqlExpression $operator '$arg'::boolean)"; break;
        }
    }
}