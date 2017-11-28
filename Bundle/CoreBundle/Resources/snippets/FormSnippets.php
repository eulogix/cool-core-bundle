<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Resources\snippets;

use Eulogix\Cool\Lib\Form\DSCRUDForm;
use Eulogix\Cool\Lib\Form\FormInterface;
use Eulogix\Cool\Lib\Widget\WidgetInterface;

use Eulogix\Cool\Lib\Annotation\SnippetMeta;

class FormSnippets
{
    /**
     * @SnippetMeta(category="form_action", contextIgnore={"widget"}, directInvocation="true", description="Set fields read only")
     *
     * @param FormInterface $widget
     * @param string $fieldNames comma separated list of field names
     */
    public static function setFieldsReadOnly(FormInterface $widget, $fieldNames)
    {
        $fields = explode(',', $fieldNames);
        foreach ($fields as $field) {
            $widget->getField($field)->setReadOnly(true);
        }
    }

    /**
     * @SnippetMeta(category="form_action", contextIgnore={"widget"}, directInvocation="true", description="Set fields mandatory")
     *
     * @param FormInterface $widget
     * @param string $fieldNames comma separated list of field names
     */
    public static function setFieldsMandatory(FormInterface $widget, $fieldNames)
    {
        $fields = explode(',', $fieldNames);
        foreach ($fields as $field) {
            $widget->getField($field)->setNotNull();
        }
    }

    /**
     * @SnippetMeta(category="form_action", contextIgnore={"widget"}, directInvocation="true", description="Validate field is GREATER THAN x")
     *
     * @param FormInterface $widget
     * @param string $field the name of the field
     * @param string $value the min value
     */
    public static function validateFieldIsGreaterThanX(FormInterface $widget, $field, $value)
    {
        $widget->getField($field)->setConstraints(\Eulogix\Lib\Validation\ConstraintBuilder::GreaterThan_($value));
    }

    /**
     * @SnippetMeta(category="form_action", contextIgnore={"widget"}, directInvocation="true", description="Validate fields are null")
     *
     * @param FormInterface $widget
     * @param string $fieldNames , separated field names
     */
    public static function validateFieldsAreNull(FormInterface $widget, $fieldNames)
    {
        $fields = explode(',', $fieldNames);
        foreach ($fields as $field) {
            $widget->getField($field)->setConstraints(\Eulogix\Lib\Validation\ConstraintBuilder::Null_());
        }
    }

    /**
     * @SnippetMeta(category="form_action", contextIgnore={"widget"}, directInvocation="true", description="Validate field is NOT in a list of values")
     *
     * @param FormInterface $widget
     * @param string $values , separated list of values
     * @param string $field the field name
     */
    public static function validateFieldIsNotInAListOfValues(FormInterface $widget, $values, $field)
    {
        $values_ = explode(',', $values);
        $widget->getField($field)->setConstraints(\Eulogix\Lib\Validation\ConstraintBuilder::In_($values_));
    }

    /**
     * @SnippetMeta(category="form_action", contextIgnore={"widget"}, directInvocation="true", description="Hide fields")
     *
     * @param FormInterface $widget
     * @param string $fieldNames Comma separated list of fields to hide
     */
    public static function hideField(FormInterface $widget, $fieldNames)
    {
        $_fieldNames = explode(',', $fieldNames);
        foreach($_fieldNames as $fieldName)
            $widget->getField($fieldName)->hide();
    }

    /**
     * @SnippetMeta(category="form_action", contextIgnore={"widget"}, directInvocation="true", description="Set field VALUE")
     *
     * @param FormInterface $widget
     * @param string $fieldName The field name
     * @param string $value the value
     */
    public static function setFieldValue(FormInterface $widget, $fieldName, $value)
    {
        $widget->getField($fieldName)->setValue($value);
    }

    /**
     * @SnippetMeta(category="form_variable", contextIgnore={"widget"}, directInvocation="true", description="Get field VALUE")
     *
     * @param FormInterface $widget
     * @param string $fieldName the field name
     *
     * @return string
     */
    public static function getFieldValue(FormInterface $widget, $fieldName)
    {
        return $widget->getField($fieldName)->getValue();
    }

    /**
     * @SnippetMeta(category="form_variable", contextIgnore={"widget"}, directInvocation="true", description="Is current record NEW ?")
     *
     * @param DSCRUDForm $widget
     *
     * @return bool
     */
    public static function isCurrentRecordNew(DSCRUDForm $widget)
    {
        return $widget->getDSRecord()->isNew();
    }

    /**
     * @SnippetMeta(category="form_variable", contextIgnore={"widget"}, directInvocation="true", description="Field has errors?", longDescription="TRUE if the field has an error (from the validator)")
     *
     * @param FormInterface $widget
     * @param string $fieldName the field name
     *
     * @return bool
     */
    public static function fieldHasErrors(FormInterface $widget, $fieldName)
    {
        return count($widget->getField($fieldName)->getErrors()) > 0;
    }

    /**
     * @SnippetMeta(category="form_action", contextIgnore={"widget"}, directInvocation="true", description="Configure number fields")
     *
     * @param FormInterface $widget
     * @param string $fieldNames comma separated list of field names
     * @param string $to maximum value (optional)
     * @param string $useSlider set to 1 to use a slider
     * @param string $from minimum value (optional)
     */
    public static function setNumberFieldsParameters(FormInterface $widget, $fieldNames, $to, $useSlider, $from)
    {
        $fieldNames_ = explode(',', $fieldNames);

        foreach ($fieldNames_ as $fieldName_) {
            $field = $widget->getField($fieldName_);
            if($field instanceof \Eulogix\Cool\Lib\Form\Field\Number) {
                if ($from !== null) {
                    $field->setFrom(intval($from));
                }
                if ($to !== null) {
                    $field->setTo(intval($to));
                }
                if ($useSlider == '1') {
                    $field->setUseSlider(true);
                }
            }
        }
    }

    /**
     * @SnippetMeta(category="form_action", contextIgnore={"widget"}, directInvocation="true", description="Validate field against regex")
     *
     * @param FormInterface $widget
     * @param string $field the field name
     * @param string $regex the regex
     * @param string $message The error message
     */
    public static function validateFieldAgainstRegex(FormInterface $widget, $field, $regex, $message)
    {
        $widget->getField($field)->setConstraints(\Eulogix\Lib\Validation\ConstraintBuilder::Regex_($regex, $message));
    }

    /**
     * @SnippetMeta(category="form_action", contextIgnore={"widget"}, directInvocation="true", description="Validate field is in list of values")
     *
     * @param FormInterface $widget
     * @param string $values comma separated list of values
     * @param string $field the name of the field
     * @param string $message The error message
     */
    public static function validateFieldIsInListOfValues(FormInterface $widget, $values, $field, $message)
    {
        $values_ = explode(',', $values);
        $widget->getField($field)->setConstraints(\Eulogix\Lib\Validation\ConstraintBuilder::In_($values_, $message));
    }

    /**
     * @SnippetMeta(category="form_action", contextIgnore={"widget"}, directInvocation="true", description="Set control to DATE")
     *
     * @param FormInterface $widget
     * @param string $fieldNames comma separated list of field names
     */
    public static function setControlToDate(FormInterface $widget, $fieldNames)
    {
        $fieldNames_ = explode(',', $fieldNames);

        foreach ($fieldNames_ as $fieldName_) {
            $widget->getField($fieldName_)->setType(\Eulogix\Cool\Lib\Form\Field\FieldInterface::TYPE_DATE);
        }
    }

    /**
     * @SnippetMeta(category="form_variable", contextIgnore={"widget"}, directInvocation="true", description="Get changed fields")
     *
     * @param DSCRUDForm $widget
     *
     * @return array
     */
    public static function getChangedFields(DSCRUDForm $widget)
    {
        return $widget->getChangedFields();
    }

    /**
     * @SnippetMeta(category="form_variable", contextIgnore={"widget"}, directInvocation="true", description="Get original values")
     *
     * @param DSCRUDForm $widget
     *
     * @return array
     */
    public static function getOriginalValues(DSCRUDForm $widget)
    {
        return $widget->getOriginalValues();
    }

    /**
     * @SnippetMeta(category="form_variable", contextIgnore={"widget"}, directInvocation="true", description="Is record just SAVED", longDescription="T/F if the record has been successfully saved")
     *
     * @param DSCRUDForm $widget
     *
     * @return bool
     */
    public static function isRecordJustSaved(DSCRUDForm $widget)
    {
        return $widget->hasEvent(\Eulogix\Cool\Lib\Form\DSCRUDForm::EVENT_RECORD_SAVED);
    }

    /**
     * @SnippetMeta(category="form_variable", contextIgnore={"widget"}, directInvocation="true", description="Is field changed")
     *
     * @param DSCRUDForm $widget
     * @param string $fieldName Field name
     *
     * @return bool
     */
    public static function isFieldChanged(DSCRUDForm $widget, $fieldName)
    {
        return isset( $widget->getChangedFields()[ $fieldName ] );
    }

    /**
     * @SnippetMeta(category="form_variable", contextIgnore={"widget"}, directInvocation="true", description="Get original field VALUE", longDescription="Get original field VALUE (textual or json_encoded)")
     *
     * @param DSCRUDForm $widget
     * @param string $fieldName fieldName
     *
     * @return string
     */
    public static function getOriginalFieldValue(DSCRUDForm $widget, $fieldName)
    {
        $ret = @$widget->getOriginalValues()[ $fieldName ];

        if ($ret instanceof \DateTime) {
            return $ret->format('c');
        }

        if (is_array($ret)) {
            return json_encode($ret);
        }

        return $ret;
    }

    /**
     * @SnippetMeta(category="form_action", contextIgnore={"widget"}, directInvocation="true", description="Set a field to a SELECT with TABLE LOOKUP", longDescription="Transforms a field to a SELECT tied to a TABLE LOOKUP")
     *
     * @param FormInterface $widget
     * @param string $domainName The domain name of the Lookup
     * @param string $fieldName The name of the field to add (or transform)
     */
    public static function setAFieldToASelectWithTableLookup(FormInterface $widget, $domainName, $fieldName)
    {
        $core = \Eulogix\Cool\Lib\Cool::getInstance()->getCoreSchema();
        $mapHash = $core->getValueMapTable($domainName);
        $vMap = new \Eulogix\Cool\Lib\DataSource\SimpleValueMap($mapHash);
        $widget->addFieldSelect($fieldName)->setValueMap($vMap);
    }

    /**
     * @SnippetMeta(category="form_action", contextIgnore={"widget"}, directInvocation="true", description="Populate select with SQL query", longDescription="Populates a fields with the results of a SQL query. The query must provide the fields 'value' and optionally 'label' and 'pk'")
     *
     * @param WidgetInterface $widget
     * @param string $schemaName the name of the schema on which to perform the query
     * @param string $fieldName the name of the field to populate
     * @param string $humanDescriptionTable (optional) if set, the label will be taken using the getHumanDescription() method of the OM class of this table, records are instantiated using the sql expression 'pk' or in alternative, 'value'
     * @param string $sql The sql expression which must contain 'value' and optionally 'label' and 'pk'
     */
    public static function populateSelectWithSqlQuery(
        WidgetInterface $widget,
        $schemaName,
        $fieldName,
        $humanDescriptionTable,
        $sql
    ) {
        try {

            $schema = \Eulogix\Cool\Lib\Cool::getInstance()->getSchema($schemaName);
            $data = $schema->fetchArray($sql);

            if ($humanDescriptionTable) {
                foreach ($data as &$option) {
                    $pk = @$option[ 'pk' ] ?? @$option[ 'value' ];
                    $obj = $schema->getPropelObject($humanDescriptionTable, $pk);
                    if (method_exists($obj, 'getHumanDescription')) {
                        $option[ 'label' ] = $obj->getHumanDescription();
                    }
                }
            }

            $field = $widget->getField($fieldName)->setType(\Eulogix\Cool\Lib\Form\Field\FieldInterface::TYPE_SELECT);
            $field->setOptions($data);

        } catch (\Exception $e) {
            $widget->addMessageError($e->getMessage());
        }
    }

}