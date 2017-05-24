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

use Eulogix\Cool\Lib\File\FileProxyInterface;
use Eulogix\Cool\Lib\File\FileRepositoryInterface;
use Eulogix\Lib\Validation\BeanValidatorInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface DataSourceInterface {

    const RECORD_IDENTIFIER = '_recordid';
    const HAS_CHILDREN_IDENTIFIER = '_has_children';
    const META_IDENTIFIER = '_meta';
    const DECODIFICATIONS_IDENTIFIER = '_decodifications';
    const RECORD_DESCRIPTION_IDENTIFIER = '_record_description';

    const META_CAN_ADD = 'canAdd';
    const META_CAN_DELETE_MULTIPLE = 'canDeleteMultiple';
    const META_CAN_EXPORT_XLSX = 'canExportXLSX';

    const META_RECORD_CAN_DELETE = 'canDeleteRecord';
    const META_RECORD_CAN_EDIT = 'canEditRecord';

    /**
     * method that must be called after instantiation
     * @param array $parameters
     * @return self
     */
    public function build($parameters=[]);

    /**
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    public function getParameters();

    /**
     * @param $name
     * @return mixed
     */
    public function getParameter($name);

    /**
     * @param $name
     * @return $this
     */
    public function removeParameter($name);

    /**
     * This method carries out the actual processing of a DataSource request.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function execute(DSRequest $req);

    /**
     * This method is called by DataSource.execute() for "add" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeAdd(DSRequest $req);

    /**
     * This method is called by DataSource.execute() for "clientExport" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeClientExport(DSRequest $req);

    /**
     * This method is called by DataSource.execute() for "custom" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeCustom(DSRequest $req);

    /**
     * This method is called by DataSource.execute() for "fetch" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeFetch(DSRequest $req);

    /**
     * This method is called by DataSource.execute() for "count" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeCount(DSRequest $req);

    /**
     * This method is called by DataSource.execute() for "remove" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeRemove(DSRequest $req);

    /**
     * This method is called by DataSource.execute() for "update" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeUpdate(DSRequest $req);
    
    /**
    * adds a field
    * @param string $fieldName
    * @param DSField|null $field
    * @return DSField
    */
    public function addField($fieldName, DSField $field=null);

    /**
     * @param DSField[] $fields
     * @return self
     */
    public function addFields($fields);

    /**
    * @param string $fieldName
    * @return boolean
    */
    public function hasField($fieldName);

    /**
    * removes a field
    * @param string $fieldName
    * @return boolean
    */
    public function removeField($fieldName);
    
    /**
    * Returns the field definition for the specified field
    * @param string $fieldName
    * @return DSField|boolean
    */
    public function getField($fieldName);

    /**
    * Returns all the fields
    * @return DSField[]
    */
    public function getFields();
    
    /**
    * Returns the list of defined fields
    * @return string[]
    */
    public function getFieldNames();
    
    /**
    *  Returns the name of the primary key field
    * @return string 
    */
    public function getPrimaryKey();

    /**
     * @return BeanValidatorInterface
     */
    public function getValidator();

    /**
    * Takes a Map of fieldName -> value (data argument) and validates the data using the validators specified on this dataSource
    * @param mixed $data an associative array of values
    * @param boolean $reportMissingRequiredFields    
    */
    public function validate($data, $reportMissingRequiredFields);

    /**
     * hook used to decode a single field of the record. The returned value is what the user is expected to see in UI, readable extractions, and so on
     *
     * @param $fieldName
     * @param $value
     * @param $recordid
     * @return mixed
     */
    public function getDecodedValue($fieldName, $value, $recordid);

    /**
     * hook used to provide a textual description for the record as a whole. Used in the UI for record selectors (eg Timeline)
     *
     * @param array $row
     * @return string
     */
    public function getRowDescription($row);

    /**
     * return a row's meta information
     * @param array $row
     * @return array
     */
    public function getRowMeta( $row );

    /**
     * return row's Files
     * @param array $row
     * @param bool $hydrate if set, the method returns a collection of File proxies
     * @return string[]|FileProxyInterface[]
     */
    public function getRowFiles( $row, $hydrate=false );

    /**
     * return ds meta information
     * @return array
     */
    public function getMeta();

    /**
     * evaluates a query expression and produces a nested function call which, eval'd from the actual implementation
     * of the datasource, produces something useful for it to actually perform the query
     * e.g an sql expression, a set of instructions...
     * the actual format of query is based on the query implementation of gridx, which is a good way of representing nested query conditions
     * @see https://github.com/oria/gridx/wiki/How-to-filter-Gridx-with-any-condition%3F
     *
     * @param array $query
     * @return mixed
     */
    public function getQueryExpression($query);

    /**
     * @return DSQuery
     */
    public function getDSQuery();

    /**
     * sets a base query that always gets ANDed to the dsquery object for fetch operations
     *
     * @param array $baseQuery
     * @return $this
     */
    public function setBaseQuery($baseQuery);

    /**
     * @return array
     */
    public function getBaseQuery();

    /**
     * @param $recordId
     * @param array $requestParameters
     * @return DSRecord
     */
    public function getDSRecord($recordId, $requestParameters = []);

    /**
     * returns the default file repository instance that retrieves and stores files, for a given fieldname or recordid
     *
     * @param mixed $recordid
     * @return FileRepositoryInterface
     */
    public function getFileRepository($recordid=null);

    /**
     * @param $bool
     * @return $this
     */
    public function setReadOnly($bool);

    /**
     * @return bool
     */
    public function isReadOnly();

    /**
     * sets the instant in time in which we want the datasource to operate.
     * Instants in the future are not allowed, while instants in the past render the DataSource read only.
     * Passing null means current (the default)
     * DataSources not implementing the TimeTransaction behaviour should raise an exception for any value other than null
     *
     * @param \DateTime|null $instant
     * @return mixed
     * @throws \Exception
     */
    public function setInstant(\DateTime $instant);

    /**
     * @return \DateTime|null
     */
    public function getInstant();

    /**
     * @return boolean
     */
    public function isInAuditMode();
}