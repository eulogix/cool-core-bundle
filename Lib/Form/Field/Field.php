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

use Eulogix\Lib\Validation\ConstraintBuilder;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Field implements FieldInterface {

    /**
     * @var string
     */
    protected $type, $name, $group, $label;

    /**
     * @var string
     */
    protected $tooltipContent, $tooltipUrl, $tooltipDelay;

    /**
     * @var integer
     */
    protected $tooltipMaxWidth;

    /**
     * @var string
     */
    protected $coolDojoWidget = "cool/controls/textbox";

    /**
    * @var ParameterBag
    */
    public $parameters;

    /**
     * @var mixed
     */
    protected $value;

    protected $errors = [];

    protected $actionButtons = [];

    /**
     * @var string[]
     */
    protected $CSSStyles = [];

    /**
     * @var string[]
     */
    protected $CSSClasses = [];

    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var callable
     */
    protected $persistableValueLambda;

    /**
     * @var ValueMapInterface
     */
    private $valueMap;

    /**
     * @param FormInterface $form
     */
    public function __construct(FormInterface $form=null)
    {
        $this->setForm($form);
        $this->init();
    }

    /**
     * performs additional initializations without having to override the constructor
     */
    protected function init() {

    }

    /**
     * @inheritdoc
     */
    public function getParameters() {
        if(!$this->parameters)
            $this->parameters = new ParameterBag();
        return $this->parameters;
    }

    /**
     * @inheritdoc
     */
    public function getForm() {
        return $this->form;
    }

    /**
     * @inheritdoc
     */
    public function setForm(FormInterface $form) {
        $this->form = $form;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDefinition() {
         $def = array(
            "type" => $this->type,
            "value" => $this->getRawValue(),
            "name" => $this->getName(),
            "label" => $this->getLabel(),

            "tooltip" => [
                'content' => $this->tooltipContent,
                'url' => $this->tooltipUrl,
                'maxWidth' => $this->tooltipMaxWidth,
                'delay' => $this->tooltipDelay
            ],

            "coolDojoWidget" => $this->getCoolDojoWidget(),
            "CSSStyles" => $this->getCSSStyles(),
            "CSSClasses" => $this->getCSSClasses()
         );
         
         if($this->getParameters()->count()>0) {
            $def['parameters'] = $this->getParameters()->all();
         }
         
         if($this->errors) {
             $def['errors'] = $this->errors;
         }

         if($this->actionButtons) {
             $def['actionButtons'] = $this->actionButtons;
         }
         
         return $def;
    }

    /**
     * @inheritdoc
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $constraints = $this->getConstraints();
        $newField = $this->getForm()->fieldFactory($type, $this->getValueMap());

        $newField   ->setName($this->getName())
                    ->setValue($this->getValue())
                    ->setLabel($this->getLabel())
                    ->setGroup($this->getGroup())
                    ->setReadOnly($this->getReadOnly())
                    ->setCSSClasses($this->getCSSClasses())
                    ->setCSSStyles($this->getCSSStyles());

        $this->getForm()->removeField($name = $this->getName());
        return $this->getForm()->addField($name, $newField)->setConstraints($constraints);
    }

    /**
     * @inheritdoc
     */
    public function hide() {
        return $this->setType(self::TYPE_HIDDEN);
    }

    /**
     * @inheritdoc
     */
    public function isHidden() {
        return $this->getType() == self::TYPE_HIDDEN;
    }

    /**
     * @inheritdoc
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setGroup($group)
    {
        $this->group = $group;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @inheritdoc
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @inheritdoc
     */
    public function setRawValue($value) {
        $this->value = $value;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRawValue() {
        return $this->value;
    }

    /**
     * @inheritdoc
     */
    public function setPersistableValueLambda(callable $lambda) {
        $this->persistableValueLambda = $lambda;
    }

    /**
     * @inheritdoc
     */
    public function getPersistableValue() {
        return $this->persistableValueLambda ? call_user_func($this->persistableValueLambda, $this->getValue()) : $this->getValue();
    }

    /**
     * @inheritdoc
     */
    public function setLabel($label) {
        $this->label = $label;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * @inheritdoc
     */
    public function setTooltip($content, $url = null, $maxWidth = 300, $delay = 200)
    {
        $this->tooltipContent = $content;
        $this->tooltipUrl = $url;
        $this->tooltipMaxWidth = $maxWidth;
        $this->tooltipDelay = $delay;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getConstraints() {
        if($form = $this->getForm()) {
            return $form->getValidator()->getConstraints( $this->getName() );
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function setConstraints( $constraints, $contexts = null ) {
        if($form = $this->getForm()) {
            $form->getValidator()->setConstraints( $this->getName(), $constraints, $contexts );
        }
        return $this;
    }

    /**
    * @inheritdoc
    */
    public function setReadOnly($value = true) {
        $this->getParameters()->set( self::PROP_READONLY, $value );
        return $this;
    }

    /**
    * @inheritdoc
    */
    public function getReadOnly() {
        return $this->getParameters()->get( self::PROP_READONLY ) === true;
    }

    /**
     * @inheritdoc
     */
    public function setOnLoad($js) {
        $this->getParameters()->set( self::EVT_ONLOAD, $js );
        return $this;
    }

    /**
    * @inheritdoc
    */
    public function setOnChange($js) {
        $this->getParameters()->set( self::EVT_ONCHANGE, $js );
        return $this;
    }

    /**
    * @inheritdoc
    */
    public function addOnChange($js) {
        $this->getParameters()->set( self::EVT_ONCHANGE, $this->getParameters()->get( self::EVT_ONCHANGE ) ."\n". $js );
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setOnChangeOrLoad($js) {
        $this->setOnChange($js)->setOnLoad($js);
        return $this;
    }
    
    /**
    * @inheritdoc
    */
    public function addError($error) {
        $this->errors[] = $error;
    }
    
    /**
    * @inheritdoc
    */
    public function getErrors() {
        return $this->errors;
    }


    /**
     * @inheritdoc
     */
    public function setValueMap(ValueMapInterface $valueMap)
    {
        $this->valueMap = $valueMap;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValueMap()
    {
        return $this->valueMap;
    }

    /**
     * @return string
     */
    protected function getCoolDojoWidget()
    {
        return $this->coolDojoWidget;
    }

    /**
     * @inheritdoc
     */
    public function addActionButton($js, $icon, $label='') {
        $this->actionButtons[] = [
            'js' => $js,
            'icon' => $icon,
            'label' => $label
        ];
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setNotNull($contexts = null)
    {
        if($this->isMultiple()) {
            $this->setConstraints(ConstraintBuilder::Count_(1));
        } else {
            $this->setConstraints(ConstraintBuilder::NotNull_(), $contexts);
        }
        $this->addCSSClass('requiredField');
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isEmpty() {
        $v = $this->getValue();
        return $v == null || $v == '';
    }

    /**
     * @inheritdoc
     */
    public function isMultiple()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function addCSSStyle($CSSStyle)
    {
        $this->CSSStyles[] = $CSSStyle;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCSSStyles()
    {
        return $this->CSSStyles;
    }


    /**
     * @inheritdoc
     */
    public function addCSSClass($CSSClass)
    {
        $this->CSSClasses[] = $CSSClass;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCSSClasses()
    {
        return $this->CSSClasses;
    }

    /**
     * @inheritdoc
     */
    public function setCSSStyles(array $CSSStyles)
    {
        $this->CSSStyles = $CSSStyles;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setCSSClasses(array $CSSClasses)
    {
        $this->CSSClasses = $CSSClasses;
        return $this;
    }
}