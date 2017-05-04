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

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class XhrStoreRequest implements StoreRequestInterface {

    private $get=[];
    private $post=[];

    /**
     * private constructor ensures that the class can be instantiated only with monadic
     * static factory methods
     */
    private function __construct($get, $post) {
        $this->get = $get;
        $this->post = $post;
    }

    /**
     * @return XhrStoreRequest
     */
    public static function fromGlobals() {
        return new XhrStoreRequest($_GET, $_POST);
    }

    /**
     * @param Request $request
     * @return XhrStoreRequest
     */
    public static function fromSymfonyRequest(Request $request) {
        return new XhrStoreRequest($request->query->all(), $request->request->all());
    }

    /**
     * @param array $get
     * @param array $post
     * @return XhrStoreRequest
     */
    public static function fromGetAndPostArrays(array $get, array $post) {
        return new XhrStoreRequest($get, $post);
    }

    /**
     * @return string
     */
    public function getOperation()
    {
        return $this->get[ self::OPERATION_PARAMETER ];
    }

    /**
     * @return int|null
     */
    public function getRangeFrom()
    {
        if(isset($this->post['_range_from']))
            return intval($this->post['_range_from']);
        return 0;
    }

    /**
     * @return int|null
     */
    public function getRangeTo()
    {
        return !empty($this->post['_range_to']) ? intval($this->post['_range_to']) : null;
    }

    /**
     * @inheritdoc
     */
    public function getSortArray()
    {
        $sort = [];
        if(isset($this->get['_sort'])) {
            if(preg_match_all('/(D|A)([^,]+)/im', $this->get['_sort'], $mm, PREG_SET_ORDER)) {
                foreach($mm as $sortToken) {
                    $sort[ $sortToken[2] ] = $sortToken[1] == 'A' ? self::SORT_SPEC_ASCENDING : self::SORT_SPEC_DESCENDING;
                }
            }
        }
        return $sort;
    }

    /**
     * @inheritdoc
     */
    public function getGridxQuery()
    {
        $query = null;
        if(isset($this->post['_query'])) {
            $query = json_decode($this->post['_query'], true);
        }
        return $query;
    }

    /**
     * @inheritdoc
     */
    public function getPostedRecord()
    {
        return json_decode($this->post['_postedObject'], true);
    }

    /**
     * returns a string that represents the object Id for REMOVE operations
     * @return string
     */
    public function getPostedObjectId()
    {
        $idProperty = @$this->post['idProperty'];
        return @$this->post[$idProperty];
    }

    /**
     * @inheritdoc
     */
    public function getParameters()
    {
        //TODO: remove reserved parameter names?
        return array_merge($this->get, $this->post);
    }

    /**
     * use this to override a get parameter
     * @param $name
     * @param $value
     */
    public function setGetParameter($name, $value) {
        $this->get[$name] = $value;
    }

    /**
     * use this to override a post parameter
     * @param $name
     * @param $value
     */
    public function setPostParameter($name, $value) {
        $this->post[$name] = $value;
    }

    /**
     * @return boolean
     */
    public function getIncludeDescriptions()
    {
        return @$this->getParameters()[self::PARAM_INCLUDE_DESCRIPTIONS] == 1;
    }
}