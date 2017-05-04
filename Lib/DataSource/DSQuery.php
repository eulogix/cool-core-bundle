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

class DSQuery {

    /**
     * @var DataSourceInterface
     */
    protected $dataSource = null;

    /**
     * @var string[]
     */
    protected $fieldNames = [];

    public function __construct(DataSourceInterface $ds) {
        $this->dataSource = $ds;
        $this->fieldNames = $ds->getFieldNames();
    }

    /**
     * @return DataSourceInterface
     */
    public function getDataSource() {
        return $this->dataSource;
    }

    private function op($op, $fieldName, $arg=null, $arg2=null) {

        $fieldOffset = array_search( $fieldName, $this->fieldNames );
        $fieldOffset+=2;

        $data = [
            ['op' => 'string', 'data' => $fieldOffset, 'isCol' => true],
            ['op' => 'string', 'data' => $arg]
        ];

        if($arg2)
            $data[] =  ['op' => 'string', 'data' => $arg2];

        return [
            'op' => $op,
            'data' => $data
        ];
    }

    public function equal($fieldName, $arg) { return $this->op('equal', $fieldName, $arg); }
    public function different($fieldName, $arg) { return $this->op('different', $fieldName, $arg); }
    public function greater($fieldName, $arg) { return $this->op('greater', $fieldName, $arg); }
    public function less($fieldName, $arg) { return $this->op('less', $fieldName, $arg); }
    public function between($fieldName, $arg, $arg2) { return $this->op('between', $fieldName, $arg, $arg2); }
    public function outside($fieldName, $arg, $arg2) { return $this->op('outside', $fieldName, $arg, $arg2); }
    public function greaterEqual($fieldName, $arg) { return $this->op('greaterEqual', $fieldName, $arg); }
    public function lessEqual($fieldName, $arg) { return $this->op('lessEqual', $fieldName, $arg); }
    public function match($fieldName, $arg) { return $this->op('match', $fieldName, $arg); }
    public function contain($fieldName, $arg) { return $this->op('contain', $fieldName, $arg); }
    public function startWith($fieldName, $arg) { return $this->op('startWith', $fieldName, $arg); }
    public function endWith($fieldName, $arg) { return $this->op('endWith', $fieldName, $arg); }
    public function isEmpty($fieldName) { return $this->op('isEmpty', $fieldName); }


    private function comp($comp, $data) {
        return array('op'=>$comp, 'data'=>$data);
    }

    public function _AND($ops) { return $this->comp('and', $ops); }
    public function _OR($ops) { return $this->comp('or', $ops); }
    public function _NOT($ops) { return $this->comp('not', $ops); }

    public function stringify($array) {
        $ret = json_encode($array);
        return $ret;
    }
}