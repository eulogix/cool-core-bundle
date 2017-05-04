<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\DataSource;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class BaseValueMap implements ValueMapInterface
{
    const VMAP_URL_PARAM_PREFIX = "vmp_";

    /**
     * @var string
     */
    private $ajaxEndPoint;

    /**
     * @var ParameterBagInterface
     */
    private $parameters;

    /**
     * @inheritdoc
     */
    public function getAllValues()
    {
        $map = $this->getMap();
        return array_column($map, 'value');
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
    public function getAjaxEndPoint()
    {
        return $this->ajaxEndPoint;
    }

    /**
     * @inheritdoc
     */
    public function setAjaxEndPoint($ajaxEndPoint)
    {
        $this->ajaxEndPoint = $ajaxEndPoint;
        return $this;
    }
    
}