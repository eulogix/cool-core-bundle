<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Form;

use ArrayIterator;
use Eulogix\Cool\Lib\DataSource\DataSourceInterface;
use Eulogix\Cool\Lib\DataSource\ValueMapInterface;
use Eulogix\Cool\Lib\File\SimpleFileProxy;
use Eulogix\Cool\Lib\File\FileProxyInterface;
use Eulogix\Cool\Lib\Widget\WidgetInterface;
use Eulogix\Cool\Lib\Form\Field\FieldInterface;
use Eulogix\Lib\Validation\BeanValidatorInterface;

interface FormInterface extends WidgetInterface {

    const ATTRIBUTE_NO_AUDIT_TRAILS     =    'no_audit_trails';

    public function rawFill($data, $skipEmptyValues=false);

    public function fill($data, $skipEmptyValues=false);

    /**
    * this method gets called when the form gets submitted, 
    */
    public function onSubmit();

    /**
     * @return BeanValidatorInterface
     */
    public function getValidator();

    /**
     * validates the form
     *
     * @param mixed $limitFields An optional array of field names, if set the validation will consider only the fields whose name is in the list
     * @param string[] $limitContexts
     * @return bool
     */
    public function validate($limitFields = false, $limitContexts = null);
    

    /**
    * sets the layout
    * @param string $layout
    */
    public function setLayout($layout);
    
    public function getLayout();
    

    
    /**
    * processes the layout by parsing it as a template, using the provided \Twig_Environment
    */
    public function getProcessedLayout();
     

    /*function getConditions(){
        return array();
    } */
    
    /**
    * returns an associative array of the field values
    * 
    * @param string[]|null $limitFields array of fields that limit the population of the returned hash
    */
    public function getValues($limitFields=null);

    /**
    * returns an associative array of the field values
    *
    * @param string[]|null $limitFields array of fields that limit the population of the returned hash
    */
    public function getRawValues($limitFields=null);
    
    /**
    * @param string $fieldName
    * @param FieldInterface $field
    * @returns FieldInterface|\Eulogix\Cool\Lib\Form\Field\File
    */
    public function addField($fieldName, $field);    
    
    /**
    * @param string $fieldName
    * @return \Eulogix\Cool\Lib\Form\FormInterface
    */
    public function removeField($fieldName);
    
    /**
    * returns a field of the form
    * 
    * @param string $fieldName
    * @returns FieldInterface|\Eulogix\Cool\Lib\Form\Field\File
    */
    public function getField($fieldName);

    /**
     * @param callable|null $lambda
     * @return ArrayIterator
     */
    public function getFields($lambda=null);

    /**
     * @param string $regex
     * @return ArrayIterator
     */
    public function getFieldsByRegex($regex);

    /**
     * @param string|null $groupName the optional group name, if set only field names belonging to the group will be returned
     * @return string[]
     */
    public function getFieldNames($groupName=null);

    /**
     * sets some fields to read only, or all the form fields if no parameter is provided
     * @param bool $readOnlyState the new state to set
     * @param string[]|FieldInterface[] $fields an array of fields or field names, null means all fields
     * @return $this
     */
    public function setFieldsReadOnly($readOnlyState = false, array $fields = null);

    /**
     * returns a new field
     *
     * @param string $fieldType
     * @param ValueMapInterface $valueMap
     * @returns FieldInterface
     */
    public function fieldFactory($fieldType, ValueMapInterface $valueMap=null);

    /**
     * @param string $name
     * @returns Field\Hidden
     */
    public function addFieldHidden($name);
    
    /**
    * @param string $name
    * @returns Field\TextBox
    */
    public function addFieldTextBox($name);        
    
    /**
    * @param string $name
    * @returns Field\TextArea
    */
    public function addFieldTextArea($name);    
    
    /**
    * @param string $name
    * @returns Field\Select
    */
    public function addFieldSelect($name);
    
    /**
    * @param string $name
    * @returns Field\MultiSelect
    */
    public function addFieldMultiSelect($name);

    /**
    * @param string $name
    * @returns Field\ListPicker
    */
    public function addFieldListPicker($name);

    /**
    * @param string $name
    * @returns Field\CheckBox
    */
    public function addFieldCheckbox($name);

    /**
    * @param string $name
    * @returns Field\XhrPicker
    */
    public function addFieldXhrPicker($name);
    
    /**
    * @param string $name
    * @returns Field\HTMLEditor
    */
    public function addFieldHTMLEditor($name);    
    
    /**
    * @param string $name
    * @returns Field\Tab
    */
    public function addFieldTab($name);
    
    /**
    * @param string $name
    * @returns Field\Button
    */
    public function addFieldSubmit($name);

    /**
    * @param string $name
    * @returns Field\Currency
    */
    public function addFieldCurrency($name);

    /**
    * @param string $name
    * @returns Field\Date
    */
    public function addFieldDate($name);

    /**
    * @param string $name
    * @returns Field\Time
    */
    public function addFieldTime($name);

    /**
    * @param string $name
    * @returns Field\DateTime
    */
    public function addFieldDateTime($name);

    /**
    * @param string $name
    * @returns Field\Number
    */
    public function addFieldNumber($name);

    /**
    * @param string $name
    * @returns Field\Button
    */
    public function addFieldButton($name);

    /**
    * @param string $name
    * @returns Field\File
    */
    public function addFieldFile($name);

    /**
    * @param string $name
    * @returns Field\RepoFile
    */
    public function addFieldRepoFile($name);

    /**
     * action that gets called whenever a control of type field is asket to download its currently stored file
     * in the request there is a parameter fieldName. This method usually should not be overriden
     */
    public function onDownloadField();

    /**
     * wrapper that allows you to override it for special cases, otherwise it forwards the call to the field instance
     *
     * @param $fieldName
     * @return FileProxyInterface|null
     */
    public function getFileFromField($fieldName);


    /**
     * @param DataSourceInterface|null $dataSource
     * @return $this
     */
    public function addDataSourceFields($dataSource=null);

    /**
     * @param $fieldName
     * @param DataSourceInterface|null $dataSource
     * @return \Eulogix\Cool\Lib\Form\Field\FieldInterface|\Eulogix\Cool\Lib\Form\Field\File
     */
    public function addDataSourceField($fieldName, $dataSource=null);
}