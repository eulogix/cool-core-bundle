<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Dojo;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface StoreRequestInterface {

    const OPERATION_PARAMETER = '_storeOp';

    const OPERATION_TYPE_GET = 'get';
    const OPERATION_TYPE_PUT = 'put';
    const OPERATION_TYPE_ADD = 'add';
    const OPERATION_TYPE_REMOVE = 'remove';
    const OPERATION_TYPE_QUERY = 'query';

    const SORT_SPEC_ASCENDING = 'A';
    const SORT_SPEC_DESCENDING = 'D';

    const PARAM_INCLUDE_DESCRIPTIONS = '_includeDescriptions';

    /**
     * @return string
     */
    public function getOperation();

    /**
     * @return int|null
     */
    public function getRangeFrom();

    /**
     * @return int|null
     */
    public function getRangeTo();

    /**
     * @return boolean
     */
    public function getIncludeDescriptions();

    /**
     * returns a sort specification array in the following format:
     * [ 'field1' = SORT_SPEC_ASCENDING|DESCENDING, 'fieldN' => ... ]
     * @return []
     */
    public function getSortArray();

    /**
     * returns an array representation of the client side gridx filter structure
     * https://github.com/oria/gridx/wiki/How-to-filter-Gridx-with-any-condition%3F
     * @return mixed|null
     */
    public function getGridxQuery();

    /**
     * returns a hash that represent the posted record for PUT operations
     * @return []
     */
    public function getPostedRecord();

    /**
     * returns a string that represents the object Id for REMOVE operations
     * @return []
     */
    public function getPostedObjectId();

    /**
     * returns the raw posted parameters hash
     * @return []
     */
    public function getParameters();
} 