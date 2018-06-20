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

use Eulogix\Cool\Lib\Form\Field\FieldInterface;
use Eulogix\Cool\Lib\Traits\FSMHolder;
use Eulogix\Cool\Lib\Widget\Message;
use Finite\StatefulInterface;

abstract class FSMForm extends Form implements StatefulInterface
{

    const STATE_FIELD = 'fstate';
    const DATA_FIELD = 'fdata';

    use FSMHolder;

    /**
     * Sets the object state
     *
     * @return string
     */
    public function getFiniteState()
    {
        return $this->getParameters()->get('state');
    }

    /**
     * Sets the object state
     *
     * @param string $state
     */
    public function setFiniteState($state)
    {
        $this->getParameters()->set('state', $state);
    }

    /**
     * @inheritdoc
     */
    public function build() {
        parent::build();

        $this->addFieldHidden(self::DATA_FIELD);

        //take initial values from GET URL request, and then from the POST request
        $this->rawFill( array_merge($this->parameters->all(),$this->request->all()), true );

        if(!$this->getFSM()) {
            $this->setFSM($this->buildFSM());
        }

        $this->getAttributes()->set('fsm_state', $this->getFSM()->getCurrentState()->getName());

        return $this;
    }

    /**
     * updates the Data hidden field with values received from the last request
     */
    protected function updateDataFromFieldValues()
    {
        $arr = $this->getData();
        foreach ($this->getFieldNames() as $fieldName) {
            if ($field = $this->getField($fieldName)) {
                if (
                    !in_array( $field->getType(), [FieldInterface::TYPE_BUTTON]) &&
                    !in_array($fieldName, [self::DATA_FIELD, self::STATE_FIELD])
                ) {
                    $arr[ $fieldName ] = $field->getRawValue();
                }
            }
        }
        $this->setData($arr);
    }

    /**
     * @param null $key
     * @return array|mixed
     */
    protected function getData($key=null)
    {
        $arr = unserialize($this->getField(self::DATA_FIELD)->getValue());
        if (!$arr) $arr = [];
        return $key ? @$arr[$key] : $arr;
    }

    /**
     * @param $data
     * @return $this
     */
    protected function setData($data)
    {
        $this->getField(self::DATA_FIELD)->setValue(serialize($data));
        $this->getRequest()->set(self::DATA_FIELD, serialize($data));
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    protected function setInData($key, $value)
    {
        $data = $this->getData();
        $data[$key] = $value;
        $this->setData($data);
        return $this;
    }

    /**
     * builds forward/back transitions between the states
     * @param array $states
     * @return array
     */
    protected function buildLinearTransitions($states) {
        $stateKeys = array_keys($states);
        $ret = [];
        for($i = 0; $i<count($stateKeys); $i++) {
            $state = $stateKeys[$i];
            $nextState = isset($stateKeys[$i+1]) ? $stateKeys[$i+1] : $stateKeys[0];
            $prevState = isset($stateKeys[$i-1]) ? $stateKeys[$i-1] : null;

            if($prevState)
                $ret[$state.'_prev'] = [
                    'from' => $state,
                    'to' => $prevState
                ];

            $ret[$state.'_next'] = [
                'from' => $state,
                'to' => $nextState
            ];
        }

        return $ret;
    }

    /**
     * @return string
     */
    protected function getStateTransitionLayoutButtons() {
        $transitions = $this->getFSM()->getCurrentState()->getTransitions();
        if($transitions[0])
            $transitions[0].='|align=left';
        $transitions[count($transitions)-1].="|align=right";
        $ret = '<FIELDS>'.implode(',',$transitions).'</FIELDS>';
        return $ret;
    }

    protected function addTransitionButtons() {
        $transitions = $this->getFSM()->getCurrentState()->getTransitions();
        foreach($transitions as $t) {
            $parameters = ['transition' => $t];
            $button = $this->addFieldButton($t)
                ->setDisabledOnClick(true);

            if(preg_match('/_next$/sim', $t))
                $button->setRightIcon('/bower_components/fugue/icons/document-page-next.png');

            if(preg_match('/_prev$/sim', $t)) {
                $button->setLeftIcon('/bower_components/fugue/icons/document-page-previous.png');
                // inhibits validation for prev button
                $parameters['_prev'] = 1;
            }

            $button->setOnClick("return widget.mixAction('changeStatus', ".json_encode($parameters).");");
        }
    }

    public function onChangeStatus() {
        $transition = $this->request->get('transition');

        $parameters = $this->request->all();
        $this->rawFill( $parameters );

        if(@$parameters['_prev'] == 1 || $this->validate( array_keys($parameters) ) ) {
            $hasErrors = false;

            switch( $status = $this->getFSM()->getCurrentState()->getName() ) {
                default: {
                    $this->updateDataFromFieldValues();
                    break;
                }
            }

            if(!$hasErrors) {
                //apply the transition
                $this->getFSM()->apply($transition);
                //save current state in the request
                $this->getRequest()->set(self::STATE_FIELD, $this->getFiniteState());
                //update the form
                $this->reBuild();
            }

            //$this->addMessage(Message::TYPE_INFO, "$transition applied.");
        } else {
            $this->addMessage(Message::TYPE_ERROR, "NOT VALIDATED");
        }
    }

}