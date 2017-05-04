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

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

trait ParametersHolder {

    /**
     * @var ParameterBag
     */
    private $parameters;

    private function initParameters() {
        if($this->parameters)
            return;
        $this->parameters = new ParameterBag();
    }

    /**
     * @return ParameterBag
     */
    public function getParameters() {
        $this->initParameters();
        return $this->parameters;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getParameter($name) {
        $this->initParameters();
        return $this->getParameters()->get($name);
    }

    /**
     * @param $name
     * @return $this
     */
    public function removeParameter($name) {
        $this->initParameters();
        $this->getParameters()->remove($name);
        return $this;
    }

}