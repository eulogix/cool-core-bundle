<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Form\Field;

use Eulogix\Cool\Lib\DataSource\ValueMapInterface;
use Eulogix\Cool\Lib\Form\FormInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface FieldInterface {
    
    const TYPE_HIDDEN       =    'hidden';
    const TYPE_TEXTBOX      =    'textbox';
    const TYPE_INTEGER      =    'integer';
    const TYPE_NUMBER       =    'number';
    const TYPE_CURRENCY     =    'currency';
    const TYPE_DATE         =    'date';
    const TYPE_TIME         =    'time';
    const TYPE_DATETIME     =    'datetime';
    const TYPE_DATERANGE    =    'daterange';
    const TYPE_TEXTAREA     =    'textarea';
    const TYPE_CHECKBOX     =    'checkbox';
    const TYPE_SELECT       =    'select';
    const TYPE_XHRPICKER    =    'xhrpicker';
    const TYPE_MULTISELECT  =    'multiselect';
    const TYPE_LISTPICKER   =    'listpicker';
    const TYPE_BUTTON       =    'button';
    const TYPE_JSONEDITOR   =    'jsoneditor';
    const TYPE_HTML         =    'html';
    const TYPE_TAB          =    'tab';
    const TYPE_FILE         =    'file';
    const TYPE_REPOFILE     =    'repofile';

    const EVT_ONCHANGE      =    'evt_onchange';
    const EVT_ONLOAD        =    'evt_onload';

    const PROP_READONLY     =    'readonly';
    const PROP_HAS_HELP     =    'has_help';

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return FieldInterface
     */
    public function setType($type);

    /**
     * sets the type to hidden
     * @return FieldInterface
     */
    public function hide();

    /**
     * @return boolean
     */
    public function isHidden();

    /**
    * returns an array describing all the form attributes and fields. This array is used by the Js Form component to render the form on the client
    * @return mixed
    */
    public function getDefinition();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $group
     * @return $this
     */
    public function setGroup($group);

    /**
     * @return string
     */
    public function getGroup();

    /**
     * setters and getters for the PHP native datatypes
     * @param string $value
     * @return self
     */
    public function setValue($value);

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * setters and getters for the raw value that is passed and retrieved from the js control
     * @param string $value
     * @return $this
     */
    public function setRawValue($value);

    /**
     * @return mixed
     */
    public function getRawValue();

    /**
     * set a lambda function that processes the output of getValue()
     * used, if set, by getPersistableValue
     * @param callable $lambda
     * @return $this
     */
    public function setPersistableValueLambda(callable $lambda);

    /**
     * returns the value as it meant to be persisted to the DB
     * @return mixed
     */
    public function getPersistableValue();

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $content A static tooltip content
     * @param string $url A URL to retrieve tooltip content. substring prop_value will be replaced with the field value at runtime
     * @param int $maxWidth
     * @param int $delay msec
     * @return FieldInterface
     */
    public function setTooltip($content, $url = null, $maxWidth = 300, $delay = 200);

    /**
     * @param string $js
     * @return $this
     */
    public function setOnLoad($js);

    /**
     * @param string $js
     * @return $this
     */
    public function setOnchange($js);

    /**
     * @param string $js
     * @return $this
     */
    public function addOnchange($js);
    
    /**
    * Convenience method that calls the getConstraints method of the form BeanValidator
    * 
    * @return mixed
    */
    public function getConstraints();

    /**
     * Convenience method that calls the setConstraints method of the form BeanValidator
     *
     * @param mixed $constraints
     * @param string[] $contexts
     * @return $this
     */
    public function setConstraints( $constraints, $contexts = null );

    /**
     * @return bool
     */
    public function isEmpty();

    /**
     * returns true if the field holds a collection of values
     * @return bool
     */
    public function isMultiple();

    /**
     * Convenience method that sets a not null constraint
     * @param string[]|string $contexts
     * @return self
     */
    public function setNotNull($contexts = null);

    /**
    * sets the ReadOnly state of the field
    * 
    * @param boolean $value
    * @return $this
    */
    public function setReadOnly($value = true);

    /**
    * gets the ReadOnly state of the field
    *
    * @return boolean
    */
    public function getReadOnly();

    /**
     * @return FormInterface
     */
    public function getForm();

    /**
     * @param FormInterface $form
     * @return $this
     */
    public function setForm(FormInterface $form);


    /**
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    public function getParameters();


    /**
    * @param string $error
    * @return $this
    */
    public function addError($error);
    
    /**
    * @return string[]
    */
    public function getErrors();


    /**
     * @param ValueMapInterface $valueMap
     * @return $this
     */
    public function setValueMap(ValueMapInterface $valueMap);

    /**
     * @return ValueMapInterface|null
     */
    public function getValueMap();

    /**
     * adds a custom little icon button to the control, which can interact with it
     *
     * @param string $js
     * @param string $icon
     * @param string $label
     * @return $this
     */
    public function addActionButton($js, $icon, $label='');

    /**
     * @param string $CSSStyle
     * @return self
     */
    public function addCSSStyle($CSSStyle);

    /**
     * @return string[]
     */
    public function getCSSStyles();

    /**
     * @param string $CSSClass
     * @return self
     */
    public function addCSSClass($CSSClass);

    /**
     * @return string[]
     */
    public function getCSSClasses();

    /**
     * @param string[] $CSSStyles
     * @return self
     */
    public function setCSSStyles(array $CSSStyles);

    /**
     * @param string[] $CSSClasses
     * @return self
     */
    public function setCSSClasses(array $CSSClasses);
         
}