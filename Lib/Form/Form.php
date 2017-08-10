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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\WidgetRule;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\DataSource\DataSourceInterface;
use Eulogix\Cool\Lib\DataSource\ValueMapInterface;
use Eulogix\Cool\Lib\Factory\Factory;
use Eulogix\Cool\Lib\Form\Field\CheckBox;
use Eulogix\Cool\Lib\Form\Field\Date;
use Eulogix\Cool\Lib\Form\Field\DateTime;
use Eulogix\Cool\Lib\Form\Field\ListPicker;
use Eulogix\Cool\Lib\Form\Field\MultiSelect;
use Eulogix\Cool\Lib\Form\Field\Number;
use Eulogix\Cool\Lib\Form\Field\Time;
use Eulogix\Cool\Lib\Form\Field\XhrPicker;
use Eulogix\Cool\Lib\Widget\Event\WidgetEvent;
use Eulogix\Lib\Error\ErrorReport;
use Eulogix\Cool\Lib\Widget\Message;
use Eulogix\Cool\Lib\Widget\Widget;
use Eulogix\Cool\Lib\Form\Field\FieldInterface;
use Eulogix\Cool\Lib\Form\Field\TextBox;
use Eulogix\Cool\Lib\Form\Field\Select;
use Eulogix\Cool\Lib\Form\Field\HTMLEditor;
use Eulogix\Cool\Lib\Form\Field\Tab;
use Eulogix\Cool\Lib\Form\Field\TextArea;
use Eulogix\Cool\Lib\Form\Field\Hidden;
use Eulogix\Cool\Lib\Form\Field\JSONEditor;
use Eulogix\Cool\Lib\Form\Field\Button;
use Eulogix\Cool\Lib\Form\Field\File as FileField;
use Eulogix\Cool\Lib\Form\Field\RepoFile;
use Eulogix\Cool\Lib\Form\Field\Currency;

use Eulogix\Cool\Lib\Form\Configurator\FormConfigurator;

use Eulogix\Lib\Validation\BeanValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

class Form extends Widget implements FormInterface {

    protected $type = "form";
    
    /**
    * @var FieldInterface[]
    */
    protected $fields = [];
    
    /**
    * @var string
    */
    private $layout;
    
    /**
     * @var FormConfigurator
     */
    protected $configurator;
    
    /**
     * @var BeanValidatorInterface
     */
    protected $validator;

    const EVENT_VALIDATION_FAILED = "event_form_validation_failed";

    //stores the set of values considered the initial state of the form
    const ATTRIBUTE_ORIGINAL_VALUES = "original_values";

    public static function getClientWidget() {
        return 'cool/form';
    }


    public function __construct($parameters = [])
    {
        parent::__construct($parameters);
        if($readOnly = @$parameters[ self::PARAM_READONLY ])
            $this->setReadOnly(true);
    }

    /**
    * clears the form
    * 
    */
    public function clear() {
        parent::clear();   
        $this->fields = [];    
        return $this;
    }
    
    /**
    * returns an array describing all the form attributes and fields. This array is used by the Js Form component to render the form on the client
    * 
    */
    public function getDefinition() {
        $d = parent::getDefinition();

        $fields = [];
        foreach($this->fields as $name => $a) {
            $fields[$name] = $a->getDefinition();
        }

        $d->setBlock('fields', $fields);

        $d->setBlock('layout', $this->getProcessedLayout());

        // enable form-wide validation constraints?
        // $d['constraints'] = $this->constraints;
        return $d;
    }
    
    /**
    * @inheritdoc
    */
    public function addField($fieldName, $field) {       
        if(!$field->getLabel()) {
            $field->setLabel( $this->getTranslator()->trans($fieldName) );
        }
        $field->setName($fieldName);
        return $this->fields[$fieldName] = $field;
    }
    
    /**
    * @inheritdoc
    */
    public function removeField($fieldName) {
        if(isset($this->fields[$fieldName]))
            unset( $this->fields[$fieldName] );
        return $this;
    }
    
    /**
    * @inheritdoc
    */
    public function getField($fieldName) {
        if(isset($this->fields[$fieldName]))
            return $this->fields[$fieldName];
        return false;        
    }

    /**
     * @inheritdoc
     */
    public function getFields($lambda=null) {
        if($lambda == null)
            return new ArrayIterator( $this->fields );

        $ret = [];
        foreach($this->fields as $fieldName => &$field)
            if(call_user_func($lambda, $fieldName, $field))
                $ret[$fieldName] = $field;

        return new ArrayIterator( $ret );
    }

    /**
     * @inheritdoc
     */
    public function getFieldsByRegex($regex) {
        return $this->getFields( function($fieldName, $field) use ($regex) {
            /** @var FieldInterface $field */
            return preg_match($regex, $fieldName);
        });
    }

    /**
     * @inheritdoc
     */
    public function getFieldNames($groupName=null)
    {
        if(!$groupName)
            return array_keys($this->fields);
        else {
            $ret = [];
            foreach($this->fields as $fieldName => $field)
                if($field->getGroup()==$groupName)
                    $ret[] = $fieldName;
            return $ret;
        }
    }

    /**
     * @inheritdoc
     */
    public function setFieldsReadOnly($readOnlyState = false, array $fields = null) {
        $wkFields = $fields ?? $this->getFields();
        foreach($wkFields as $field) {
            if($field instanceof FieldInterface)
                $field->setReadOnly($readOnlyState);
            else $this->getField($field)->setReadOnly($readOnlyState);
        }
        return $this;
    }

    /**
    * @inheritdoc
    */
    public function getValues($limitFields=false) 
    {
        $array = [];
        foreach($this->fields as $name => $field) {
             if( /*!$field['parameters'][FForm::Prop_Disabled] &&*/
                (!$limitFields || array_search($name,$limitFields)!==FALSE) )  { 
                    $pv = $field->getPersistableValue();
                    $value = $pv === '' ? null : $pv;
                    $array[ $name ] = $value;
                }
        }
        return $array;
    }

    /**
    * @inheritdoc
    */
    public function getRawValues($limitFields=null)
    {
        $array = [];
        foreach($this->fields as $name => $field) {
             if( /*!$field['parameters'][FForm::Prop_Disabled] &&*/
                (!is_array($limitFields) || array_search($name,$limitFields)!==FALSE) )  {

                    $array[ $name ] = $field->getRawValue();
                }
        }
        return $array;
    }

    /**
     * being $data a hashmap with values, sets all the fields with the values from $data
     *
     * @param mixed $data
     * @param bool $skipEmptyValues
     */
    public function fill($data, $skipEmptyValues=false) {

        foreach($data as $k=>$v)
            if(!$skipEmptyValues || !empty($v)) {
                if($f = $this->getField($k)) {
                    $f->setValue($v);
                } //?
        }

        if(!$this->getOriginalValues())
            $this->storeOriginalValues();
    }

    /**
     * being $data a hashmap with values, sets all the fields with the values from $data
     *
     * @param mixed $data
     * @param bool $skipEmptyValues
     */
    public function rawFill($data, $skipEmptyValues=false) {

        foreach($data as $k=>$v)
            if(!$skipEmptyValues || !empty($v)) {
                if($f = $this->getField($k)) {
                    $f->setRawValue($v);
                } //?
        }

        if(!$this->getOriginalValues())
            $this->storeOriginalValues();
    }
    
    /**
    * @inheritdoc
    */
    public function onSubmit() {}

    /**
     * @inheritdoc
     */
    public function getValidator() {
        if(!$this->validator) {
            $this->validator = Factory::getNewBeanValidator();
        }
        return $this->validator;
    }

    /**
     * @inheritdoc
     */
    public function validate($limitFields = false, $limitContexts = null) {

        $this->processRules(WidgetRule::EVALUATION_TYPE_BEFORE_VALIDATION);

        $valueHash = $this->getValues($limitFields);
        $validator = $this->getValidator();

        if($limitContexts)
            $validator->setOperatingContexts($limitContexts);

        $errorList = $validator->validateHash( $valueHash );
        if($errorList->getFlatViolations()->count() > 0) {
            $allViolations = $errorList->getViolations();
            foreach($allViolations as $fieldName => $fieldViolations) {
                foreach($fieldViolations as $violation) {
                    /** @var $violation ConstraintViolationInterface */
                    $this->getField($fieldName)->addError( $violation->getMessage() );
                }
            }
            $this->dispatcher->dispatch(self::EVENT_VALIDATION_FAILED, new WidgetEvent($this, ['errorList' => $errorList]));
            return false;
        }

        return true;
    }

    public function getDefaultLayout() {
        $lay = "<FIELDS>";
        $fieldCount = count($this->fields);

        if($fieldCount > 40)
            $cols = 5;
        elseif($fieldCount > 20)
            $cols = 4;
        elseif($fieldCount > 12)
            $cols = 3;
        elseif($fieldCount > 4)
            $cols = 2;
        else $cols = 1;

        $colCounter = 0;
        $row = [];

        foreach($this->fields as $name => $a) {

            if($a->getType()!=FieldInterface::TYPE_BUTTON) {

                $row[] = $name;
                if(++$colCounter >= $cols) {
                    $lay.=implode(',',$row)."\n";
                    $row = [];
                    $colCounter = 0;
                }

            }
        }

        if(count($row)>0)
            $lay.=implode(',',$row)."\n";
        $row = [];
        $colCounter = 0;

        $lay.="</FIELDS>\n<FIELDS>";

        foreach($this->fields as $name => $a) {

            if($a->getType()==FieldInterface::TYPE_BUTTON) {

                $row[] = $name."|align=center";
                if(++$colCounter >= $cols) {
                    $lay.=implode(',',$row)."\n";
                    $row = [];
                    $colCounter = 0;
                }

            }
        }

        if(count($row)>0)
            $lay.=implode(',',$row)."\n";

        $lay.="</FIELDS>";
        
        return $lay;
    }
    
    public function setLayout($layout) {
        $this->layout = $layout;
        return $this;
    }
    
    public function getLayout() {
        return $this->layout ? $this->layout : $this->getDefaultLayout();
    }
    
    /**
    * @inheritdoc
    */
    public function getProcessedLayout() {
        if( $this->twig ) {
            try {
                //$this->twig->setLoader(new \Twig_Loader_String());
                $l = get_class($this->twig->getLoader());

                $layout = $this->getLayout();
                if(!$layout)
                    $layout = "EMPTY LAYOUT!";

                $twigTemplate = $this->twig->createTemplate($layout);

                $processedLayout = $twigTemplate->render( array(
                                        //TODO: pass here field values, form parameters, conditions and translation domains
                                        'widgetId' => $this->getClientId(),
                                        'coolTranslator' => $this->getTranslator(),
                                        'values' => $this->getValues(),
                                        'parameters' => $this->getParameters()->all(),
                                        'attributes' => $this->getAttributes()->all(),
                                        'serverAttributes' => $this->getServerAttributes()->all()
                                    ));
            } catch(\Exception $e) {
                throw $e;
                $processedLayout = "$l TWIG ERROR : {$e->getMessage()}";
            }
            return $processedLayout;
        }
        return $this->getLayout();
    }
    
    /**
    * @inheritdoc
    */
    public function getConfigurator() {
        $c = new FormConfigurator( $this );
        return $c;
    }
    
    /**
    * @inheritdoc
    */
    public function fieldFactory($fieldType, ValueMapInterface $valueMap=null) {
        switch($fieldType) {
            case FieldInterface::TYPE_SELECT    : {
                /*if($valueMap && $valueMap->getValuesNumber() > 50)
                     $ret = new XhrPicker($this);
                else*/
                    $ret = new Select($this);
                break;
            }
            case FieldInterface::TYPE_MULTISELECT : $ret = new MultiSelect($this); break;
            case FieldInterface::TYPE_LISTPICKER : $ret = new ListPicker($this); break;
            case FieldInterface::TYPE_XHRPICKER : $ret = new XhrPicker($this); break;
            case FieldInterface::TYPE_BUTTON    : $ret = new Button($this); break;
            case FieldInterface::TYPE_HTML      : $ret = new HTMLEditor($this); break;
            case FieldInterface::TYPE_TAB       : $ret = new Tab($this); break;
            case FieldInterface::TYPE_TEXTAREA  : $ret = new TextArea($this); break;
            case FieldInterface::TYPE_HIDDEN    : $ret = new Hidden($this); break;
            case FieldInterface::TYPE_JSONEDITOR: $ret = new JSONEditor($this); break;
            case FieldInterface::TYPE_CHECKBOX  : $ret = new CheckBox($this); break;
            case FieldInterface::TYPE_FILE      : $ret = new FileField($this); break;
            case FieldInterface::TYPE_REPOFILE  : $ret = new RepoFile($this); break;
            case FieldInterface::TYPE_DATETIME  : $ret = new DateTime($this); break;
            case FieldInterface::TYPE_DATE      : $ret = new Date($this); break;
            case FieldInterface::TYPE_TIME      : $ret = new Time($this); break;
            case FieldInterface::TYPE_NUMBER    : $ret = new Number($this); break;
            case FieldInterface::TYPE_CURRENCY  : $ret = new Currency($this); break;
            default: $ret = new TextBox($this);
        }
        if($valueMap)
            $ret->setValueMap($valueMap);
        return $ret;
    }

    /**
     * @param string $name
     * @returns Field\Hidden
     */
    public function addFieldHidden($name) {
        $field = $this->fieldFactory(FieldInterface::TYPE_HIDDEN);
        $this->addField($name, $field);
        return $field;
    }

    /**
     * @param string $name
     * @returns Field\TextBox
     */
    public function addFieldTextBox($name) {
        $field = $this->fieldFactory(FieldInterface::TYPE_TEXTBOX);
        $this->addField($name, $field);
        return $field;    
    }

    /**
     * @param string $name
     * @returns Field\TextArea
     */
    public function addFieldTextArea($name) {
        $field = $this->fieldFactory(FieldInterface::TYPE_TEXTAREA);
        $this->addField($name, $field);
        return $field;    
    }

    /**
     * @param string $name
     * @returns Field\Select
     */
    public function addFieldSelect($name) {
        $field = $this->fieldFactory(FieldInterface::TYPE_SELECT);
        $this->addField($name, $field);
        return $field;    
    }

    /**
     * @param string $name
     * @returns Field\MultiSelect
     */
    public function addFieldMultiSelect($name) {
        $field = $this->fieldFactory(FieldInterface::TYPE_MULTISELECT);
        $this->addField($name, $field);
        return $field;
    }

    /**
     * @param string $name
     * @returns Field\ListPicker
     */
    public function addFieldListPicker($name) {
        $field = $this->fieldFactory(FieldInterface::TYPE_LISTPICKER);
        $this->addField($name, $field);
        return $field;
    }

    /**
     * @param string $name
     * @returns Field\CheckBox
     */
    public function addFieldCheckbox($name) {
        $field = $this->fieldFactory(FieldInterface::TYPE_CHECKBOX);
        $this->addField($name, $field);
        return $field;
    }

    /**
     * @param string $name
     * @returns Field\XhrPicker
     */
    public function addFieldXhrPicker($name) {
        $field = $this->fieldFactory(FieldInterface::TYPE_XHRPICKER);
        $this->addField($name, $field);
        return $field;
    }

    /**
     * @param string $name
     * @returns Field\HTMLEditor
     */
    public function addFieldHTMLEditor($name) {
        $field = $this->fieldFactory(FieldInterface::TYPE_HTML);
        $this->addField($name, $field);
        return $field;
    }

    /**
     * @param string $name
     * @returns Field\Tab
     */
    public function addFieldTab($name) {
        $field = $this->fieldFactory(FieldInterface::TYPE_TAB);
        $this->addField($name, $field);
        return $field;
    }

    /**
     * @param string $name
     * @returns Field\Button
     */
    public function addFieldSubmit($name) {
        return $this->addFieldButton($name)->setOnClick('widget.submit();');
    }

    /**
     * @param string $name
     * @returns Field\Currency
     */
    public function addFieldCurrency($name)
    {
        $field = $this->fieldFactory(FieldInterface::TYPE_CURRENCY);
        $this->addField($name, $field);
        return $field;
    }

    /**
     * @param string $name
     * @returns Field\Date
     */
    public function addFieldDate($name)
    {
        $field = $this->fieldFactory(FieldInterface::TYPE_DATE);
        $this->addField($name, $field);
        return $field;
    }

    /**
     * @param string $name
     * @returns Field\Time
     */
    public function addFieldTime($name)
    {
        $field = $this->fieldFactory(FieldInterface::TYPE_TIME);
        $this->addField($name, $field);
        return $field;
    }

    /**
     * @param string $name
     * @returns Field\DateTime
     */
    public function addFieldDateTime($name)
    {
        $field = $this->fieldFactory(FieldInterface::TYPE_DATETIME);
        $this->addField($name, $field);
        return $field;
    }

    /**
     * @param string $name
     * @returns Field\Number
     */
    public function addFieldNumber($name)
    {
        $field = $this->fieldFactory(FieldInterface::TYPE_NUMBER);
        $this->addField($name, $field);
        return $field;
    }

    /**
     * @param string $name
     * @returns Field\Button
     */
    public function addFieldButton($name) {
        $field = $this->fieldFactory(FieldInterface::TYPE_BUTTON);
        //a safe default
        $field->setOnClick("return widget.callAction('$name');")
              ->setDisabledOnClick(true);
        $this->addField($name, $field);
        return $field;    
    }

    /**
     * @param string $name
     * @returns Field\File
     */
    public function addFieldFile($name) {
        $field = $this->fieldFactory(FieldInterface::TYPE_FILE);
        $this->addField($name, $field);
        return $field;
    }

    /**
     * @param string $name
     * @returns Field\RepoFile
     */
    public function addFieldRepoFile($name) {
        $field = $this->fieldFactory(FieldInterface::TYPE_REPOFILE);
        $this->addField($name, $field);
        return $field;
    }

    /**
     * @inheritdoc
     */
    public function onDownloadField() {
        if($f = $this->getFileFromField($this->request->get('fieldName'))) {
            $url = Cool::getInstance()->getFactory()->getFileTempManager()->getDownloadUrlFromFileProxy($f);
            if($url)
                return $url;
        }
        $this->addMessage(Message::TYPE_ERROR, "FILE_NOT_FOUND");
    }

    /**
     * @inheritdoc
     */
    public function getFileFromField($fieldName) {
        if($field = $this->getField($fieldName)) {
            if($field->getType()==$field::TYPE_FILE) {
                return $field->getStoredFile();
            }
        }
        return null;
    }


    /**
     * Adds to the form all the fields of the related dataSource
     * @param DataSourceInterface|null $dataSource
     * @returns CoolForm
     */
    public function addDataSourceFields($dataSource=null) {
        $ds = $dataSource ? $dataSource : $this->getDataSource();
        if( $ds && ($fields = $ds->getFieldNames())) {
            foreach($fields as $fieldName) {

                $this->addDataSourceField($fieldName, $ds);

            }
        }
        return $this;
    }

    /**
     * @param $fieldName
     * @param DataSourceInterface|null $dataSource
     * @return \Eulogix\Cool\Lib\Form\Field\FieldInterface|\Eulogix\Cool\Lib\Form\Field\File
     * @throws \Exception
     */
    public function addDataSourceField($fieldName, $dataSource=null)
    {
        $ds = $dataSource ? $dataSource : $this->getDataSource();
        if( $ds &&
            ($dsField = $ds->getField($fieldName)) ) {

            $dsVmap = $dsField->getValueMap();

            $formField = $this->addField( $fieldName, $this->fieldFactory( $dsField->getControlType(), $dsVmap ));

            if($ds->isInAuditMode() || $dsField->isReadOnly() || $ds->isReadOnly() || $dsField->isAutoGenerated()) {
                $formField->setReadOnly(true);
            }

            if($dsField->isRequired()) {
                $formField->setNotNull();
                //$formField->getParameters()->set('required', true); //propagates to dojo required property, disabled as it has no effect (probably it requires the use of dojo's mechanisms to submit forms
            }

            if($dsField->getDefaultValue()!==null)
                $formField->setValue($dsField->getDefaultValue());

            if($formField->getType()==$formField::TYPE_FILE) {
                //TODO: what repository should we set here?
                //$formField->setFileRepository($ds->getFileRepository($this->getRecordId()));
            }

            /*foreach($fieldSettings as $settingName=>$settingValue) {
                switch($settingName) {
                    case Dictionary::COL_ATT_JSON_SCHEMA : {
                        $field->parameters->set($settingName, $settingValue);
                        break;
                    }
                }
            }*/
        } else throw new \Exception("Bad field name $fieldName or missing dataSource");

        return $formField;
    }

    /**
     * @param \Eulogix\Lib\Error\ErrorReport $errorReport
     * @return mixed|void
     */
    public function mergeErrorReport(ErrorReport $errorReport)
    {
        $errors = $errorReport->getErrors();
        foreach ($errors as $fieldName => $error) {
            if($field = $this->getField($fieldName))
                $field->addError($error);
            else $this->addMessage(Message::TYPE_ERROR, $error);
        }

        $generalErrors = $errorReport->getGeneralErrors();
        foreach ($generalErrors as $error) {
            $this->addMessage(Message::TYPE_ERROR, $error);
        }
    }

    /**
     * @param array $values
     */
    public function storeOriginalValues(array $values = null) {
        $this->getServerAttributes()->set( self::ATTRIBUTE_ORIGINAL_VALUES, $values ?? $this->getValues() );
    }

    /**
     * @return array
     */
    public function getOriginalValues() {
        return $this->getServerAttributes()->get( self::ATTRIBUTE_ORIGINAL_VALUES ) ?? [];
    }

    /**
     * @return array
     */
    public function getChangedFields() {
        $oldValues = $this->getOriginalValues();
        $newValues = $this->getValues();

        $changedFields = [];
        foreach($oldValues as $fieldName => $fieldValue)
            if($fieldValue != @$newValues[$fieldName])
                $changedFields[$fieldName] = [
                    'old' => $fieldValue,
                    'new' => @$newValues[$fieldName]
                ];
        return $changedFields;
    }

    /**
     * @return array
     */
    public function getRuleContext() {
        return array_merge(parent::getRuleContext(), [
            'fields' => $this->getValues(),
            'changedFields' => $this->getChangedFields()
        ]);
    }

}