<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Lister\Filter;

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\DataSource\DSField;
use Eulogix\Cool\Lib\DataSource\DSRequest;
use Eulogix\Cool\Lib\DataSource\SimpleValueMap;
use Eulogix\Cool\Lib\DataSource\ValueMapInterface;
use Eulogix\Cool\Lib\Form\Event\FormEvent;
use Eulogix\Cool\Lib\Form\Field\FieldInterface;
use Eulogix\Cool\Lib\Form\Field\Select;
use Eulogix\Cool\Lib\Form\Field\Tab;
use Eulogix\Cool\Lib\Form\Form;
use Eulogix\Cool\Lib\Lister\ListerInterface;
use Eulogix\Cool\Lib\Widget\Message;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class BaseFilterForm extends Form {

    const MODE_NORMAL = 'normal';
    const MODE_ADVANCED = 'advanced';

    const ALL = "ALL";

    const SPEC_VALUE_EMPTY = '_EMPTY_';


    const EVENT_FILTER_APPLIED = "filter_applied";

    /**
     * @var ListerInterface
     */
    private $lister;

    protected function getMode() {
        return $this->getParameters()->get('mode') == self::MODE_ADVANCED ? self::MODE_ADVANCED : self::MODE_NORMAL;
    }

    /**
     * @inheritdoc
     */
    public function getVariationLevels()
    {
        return [
            'mode' => [self::MODE_NORMAL,self::MODE_ADVANCED],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getActiveLevelVariant($level)
    {
        switch ($level) {
            case 'mode': return $this->getMode();
        }
    }

    /**
     * @param array $parameters
     * @throws \Exception
     */
    public function __construct($parameters = [])
    {
        parent::__construct($parameters);
        if(!$this->parameters->has('_parent')) {
            throw new \Exception("missing parent serverId");
        }
    }

    /**
     * @inheritdoc
     */
    public function build() {
        $parentLister = $this->getWidgetFactory()->getWidget($this->getParameters()->get('_parent'), $this->getParameters()->all());
        $this->lister = $parentLister;

        parent::build();

        $this->setDataSource( $this->lister->getDataSource() );
        $this->addDataSourceFields();

        $this->addFieldSubmit("search");
        $this->addFieldButton("reset")->setOnClick("widget.callAction('reset');");

        if($this->getMode() == self::MODE_ADVANCED)
             $this->addAction('normalMode')->setOnClick("widget.callAction('normalMode');");
        else $this->addAction('advancedMode')->setOnClick("widget.callAction('advancedMode');");

        return $this;
    }

    public function refreshQuery() {
        $this->getAttributes()->set('_query', $this->getQuery());
    }

    /**
     * @inheritdoc
     */
    public function addDataSourceFields($dataSource=null) {
        if( ($ds = $this->getDataSource()) && ($fields = $ds->getFieldNames())) {
            foreach($fields as $fieldName) {
                $dsField = $ds->getField($fieldName);
                if(!in_array($dsField->getControlType(), [FieldInterface::TYPE_FILE, FieldInterface::TYPE_REPOFILE]))
                    if($this->getMode() == self::MODE_ADVANCED)
                         $this->addAdvancedFieldsFor($dsField);
                    else $this->addBaseFieldsFor($dsField);
            }
        }

        return $this;
    }

    /**
     * @param DSField $dsField
     */
    protected function addAdvancedFieldsFor(DSField $dsField) {
        $fieldName = $dsField->getName();
        $controlType = $dsField->getControlType();
        $specField = $fieldName.'_search_spec';
        $toFieldName = $fieldName.'_to';

        $map = [];

        switch($controlType) {
            case FieldInterface::TYPE_XHRPICKER:
            case FieldInterface::TYPE_SELECT: {
                    $formField = $this->addFieldMultiSelect($fieldName)
                                      ->setUseChosen(true);
                    if($vm = $dsField->getValueMap())
                        $formField->setValueMap($vm);
                    else $this->addMessageInfo("Field $fieldName does not have a valid valueMap");
                    $map = ['equal','different'];
                break;
            }
            default: {
                $formField = $this->addBaseFieldsFor($dsField);
                switch($dsField->getMacroType()) {
                    case DSField::MACRO_TYPE_STRING: $map = ['equal','different','match','contain','startWith','endWith']; break;
                    case DSField::MACRO_TYPE_NUMERIC: $map = ['equal','different','greater','less','greaterEqual','lessEqual','match','contain','startWith','endWith','between','outside']; break;
                    case DSField::MACRO_TYPE_DATETIME: $map = ['equal','different','greater','less','greaterEqual','lessEqual','between','outside']; break;
                    case DSField::MACRO_TYPE_BOOLEAN : $map = ['equal','isEmpty'];  break;
                }

                //_to field for ranges
                switch($dsField->getMacroType()) {
                    case DSField::MACRO_TYPE_NUMERIC:
                    case DSField::MACRO_TYPE_DATETIME: {
                        $this->addField($toFieldName , $this->searchFieldFactory( $controlType ));
                        break;
                    }
                }
            }
        }

        $formField->setOnChange("
            var f = container.getField('$fieldName');
            var sf = container.getField('$specField');
            if(!f.get('value')) {
                sf.set('value', null);
            } else if(!sf.get('value'))
                sf.set('value','equal');
        ");

        $this->addFieldSelect( $specField )
            ->setValueMap( new SimpleValueMap($map, Cool::getInstance()->getFactory()->getGlobalTranslator()) )
            ->setOnChangeOrLoad("
                var tof = container.getField('{$toFieldName}');
                if(tof) {
                    if(['between','outside'].indexOf( control.get('value') )  == -1)
                         tof.disable();
                    else tof.enable();
                }
            ");

    }

    /**
     * @param DSField $dsField
     * @return FieldInterface
     */
    protected function addBaseFieldsFor(DSField $dsField) {
        $fieldName = $dsField->getName();
        $controlType = $dsField->getControlType();
        $gTrans = Cool::getInstance()->getFactory()->getGlobalTranslator();

        switch($controlType) {
            case FieldInterface::TYPE_CHECKBOX: {
                return $this->addFieldSelect( $fieldName )
                    ->setValueMap( new SimpleValueMap([
                            'true','false'
                        ], $gTrans) );
                break;
            }
            default: {
                $formField = $this->addField($fieldName , $this->searchFieldFactory( $controlType, $dsField->getValueMap() ));
                if($formField instanceof Select) {
                    $formField->addOption(self::SPEC_VALUE_EMPTY, $gTrans->trans(self::SPEC_VALUE_EMPTY));
                }
                return $formField;
            }
        }
    }

    /**
     * @param $fieldType
     * @param ValueMapInterface|null $valueMap
     * @return FieldInterface
     */
    public function searchFieldFactory($fieldType, ValueMapInterface $valueMap=null) {
        switch($fieldType) {
            case FieldInterface::TYPE_TEXTAREA : $searchFieldType = FieldInterface::TYPE_TEXTBOX; break;
            case FieldInterface::TYPE_DATETIME : $searchFieldType = FieldInterface::TYPE_DATE; break;
            default: $searchFieldType = $fieldType;
        }
        return parent::fieldFactory($searchFieldType, $valueMap);
    }

    /**
     * return a different ID so that customization of form has the right namespace
    * @inheritdoc
    */
    public function getId() {
        return $this->lister->getId().'_FILTER';
    }

    /**
     * use the same translator of the lister since 99% of the times you want the same strings on them
     * @inheritdoc
     */
    public function getTranslator() {
        return $this->lister->getTranslator();
    }

    /**
     * @return string
     */
    protected function getQuery() {

        $dsQuery = $this->getDataSource()->getDSQuery();

        $ops = [];

        if( ($ds = $this->getDataSource()) && ($fields = $ds->getFieldNames())) {
            foreach($fields as $fieldName) {
                if($formField = $this->getField($fieldName)) {
                    $val = $formField->getValue();
                    $toValue = null;
                    if($val) {
                        if($this->getMode() == self::MODE_ADVANCED) {
                            $spec = $this->getField($fieldName . '_search_spec')->getValue();
                            if($tof = $this->getField($fieldName . '_to')) $toValue = $tof->getValue();
                        } else {
                            //free text fields should perform a case insensitive LIKE search, while all the others
                            //perform a plain equal comparison
                            if(in_array($formField->getType(),[FieldInterface::TYPE_TEXTAREA, FieldInterface::TYPE_TEXTBOX]))
                                $spec = 'contain';
                            elseif($val == self::SPEC_VALUE_EMPTY)
                                $spec = 'isEmpty';
                            else $spec = 'equal';
                        }

                        if($spec && method_exists($dsQuery, $spec)) {
                            if(is_array($val)) {
                                $inOps = [];
                                foreach($val as $inVal)
                                    $inOps[] = $dsQuery->$spec($fieldName, $inVal);
                                $ops[] = $spec == 'equal' ? $dsQuery->_OR($inOps) : $dsQuery->_AND($inOps);
                            } else {
                                $ops[] = $toValue ? $dsQuery->$spec($fieldName, $val, $toValue) : $dsQuery->$spec($fieldName, $val);
                            }
                        }
                    }
                }
            }
        }

        if(empty($ops))
            return '';

        $qq = $dsQuery->_AND($ops);
        return $dsQuery->stringify($qq);
    }

    /**
     * @inheritdoc
     */
    public function onSubmit() {
        $parameters = $this->request->all();
        $this->rawFill( $parameters );
        if($this->validate( array_keys($parameters) ) ) {
            
            $this->refreshQuery();
            $this->addEvent("filterLinkedLister");
            $this->dispatcher->dispatch(self::EVENT_FILTER_APPLIED, new FormEvent($this));

        } else {
            $this->addMessage(Message::TYPE_ERROR, "FILTER ERROR {1}","code!");
        }
    }

    public function onReset() {
        $parameters = $this->request->all();

        $fields = $this->getFields();
        foreach($fields as $field) {
            /** @var FieldInterface $field */
            if($field->getReadOnly() || $field->getType() == FieldInterface::TYPE_HIDDEN) {
                if($submittedValue = @$parameters[$field->getName()])
                    $field->setRawValue( $submittedValue );
            }
        }

        if($this->validate( array_keys($parameters) ) ) {

            $this->refreshQuery();
            $this->addEvent("filterLinkedLister");

        } else {
            $this->addMessage(Message::TYPE_ERROR, "FILTER ERROR {1}","code!");
        }
    }

    public function onAdvancedMode() {
        $this->getParameters()->set('mode', self::MODE_ADVANCED);
        $this->reBuild();
    }

    public function onNormalMode() {
        $this->getParameters()->set('mode', null);
        $this->reBuild();
    }

    /**
     * custom layouting for these filter forms
     * @return string
     */
    public function getDefaultLayout() {

        if($this->getMode()==self::MODE_NORMAL)
            return parent::getDefaultLayout();

        $dsNames = $this->getDataSource()->getFieldNames();

        $lay = "";
        $fieldCount = count($dsNames);

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

            if (in_array($name, $dsNames)) {
                if ($this->getField($name . '_to')) {
                    $row[] = "{$name};;{$name}_to, {$name}_search_spec:100|nolabel";
                } else {
                    $row[] = "{$name},{$name}_search_spec:100|nolabel";
                }
                if (++$colCounter >= $cols) {
                    $lay .= implode(',', $row) . "\n";
                    $row = [];
                    $colCounter = 0;
                }
            }
        }
        if(count($row)>0)
            $lay .= implode(',', $row) . "\n";

        $lay = "<FIELDS>$lay</FIELDS>
                <FIELDS>search|align=center, reset|align=center</FIELDS>";

        return $lay;
    }

    /**
     * @return ListerInterface
     */
    public function getLister() {
        return $this->lister;
    }

    /**
     * filters a ValueMap by requesting a count to the lister grouped by value.
     * values in the vmap that have no rows in the data are discarded, and a counter is added.
     * useful to display tabfilters
     *
     * @param ValueMapInterface $vmap
     * @param $dsFieldName
     * @param string[] $excludeFields
     * @return ValueMapInterface
     */
    public function filterValueMapOnListerData(ValueMapInterface $vmap, $dsFieldName, $excludeFields = []) {

        $rawValues = $this->getRawValues();
        foreach($excludeFields as $ek)
            unset($rawValues[$ek]);

        $requestParameters = array_merge(
            $this->getLister()->getParameters()->all(),
            [
                '_filter_raw_values' => json_encode($rawValues)
            ]
        );

        $dsr = new DSRequest();
        $dsr->setOperationType(DSRequest::OPERATION_TYPE_COUNT)
            ->setGroupCountFields([$dsFieldName])
            ->setQuery(json_decode($this->getAttributes()->get('_query'),true))
            ->setParameters($requestParameters);

        $response = $this->getLister()->getDataSource()->execute($dsr);

        $options = $vmap->getMap();
        $counts = $response->getData();

        $filteredOptions = [];
        if($counts !== null)
            foreach($options as $label => $option)
                foreach($counts as $c)
                    if($c['count']>0 && $c[$dsFieldName] == $option['value']) {
                        $option['label'] = "{$option['label']} ({$c['count']})";
                        $filteredOptions[] = $option;
                    }

        return new SimpleValueMap($filteredOptions);
    }

    /**
     * @param $tabFieldName
     * @param $dsFieldName
     * @param ValueMapInterface $vmap
     * @param bool $addAll
     * @throws \Exception
     */
    public function addCountersToTab($tabFieldName, $dsFieldName, ValueMapInterface $vmap = null, $addAll = true) {

        $originalVmap = $vmap ? $vmap : $this->getLister()->getDataSource()->getField($dsFieldName)->getValueMap();

        $filteredOptions = ($addAll ? [ $this->getTranslator()->trans(self::ALL) =>  self::ALL ] : []) +
                            $this->filterValueMapOnListerData( $originalVmap, $dsFieldName, [$tabFieldName])->getMap();

        /** @var Tab $field */
        $field = $this->getField($tabFieldName);
        $field->setOptions($filteredOptions);

    }
}