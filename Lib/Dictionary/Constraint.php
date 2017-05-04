<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Dictionary;

use Eulogix\Cool\Lib\DataSource\Bean;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Constraint extends Bean {

    private $type,
        $regex,
        $regex_modifiers,
        $arg,
        $arg2,
        $name,
        $message;

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $arg
     */
    public function setArg($arg)
    {
        $this->arg = $arg;
    }

    /**
     * @return mixed
     */
    public function getArg()
    {
        return $this->arg;
    }

    /**
     * @param mixed $arg2
     */
    public function setArg2($arg2)
    {
        $this->arg2 = $arg2;
    }

    /**
     * @return mixed
     */
    public function getArg2()
    {
        return $this->arg2;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $regex
     */
    public function setRegex($regex)
    {
        $this->regex = $regex;
    }

    /**
     * @return mixed
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * @param mixed $regex_modifiers
     */
    public function setRegexModifiers($regex_modifiers)
    {
        $this->regex_modifiers = $regex_modifiers;
    }

    /**
     * @return mixed
     */
    public function getRegexModifiers()
    {
        return $this->regex_modifiers;
    }



}