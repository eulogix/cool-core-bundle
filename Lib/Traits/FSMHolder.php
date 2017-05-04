<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Traits;

use Finite\StateMachine\StateMachineInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

trait FSMHolder {

    /**
     * @var StateMachineInterface
     */
    private $FSM;

    /**
     * @param StateMachineInterface $FSM
     * @return $this
     */
    public function setFSM($FSM)
    {
        $this->FSM = $FSM;
        return $this;
    }

    /**
     * @return StateMachineInterface
     */
    public function getFSM()
    {
        if(!$this->FSM)
            $this->FSM = $this->buildFSM();
        return $this->FSM;
    }

    /**
     * @return string
     */
    abstract public function getFiniteState();

    /**
     * @param string $state
     */
    abstract public function setFiniteState($state);

    /**
     * override this method to use this hook to lazily create the FSM object
     *
     * @return StateMachineInterface|null
     */
    protected function buildFSM() { return null; }

}