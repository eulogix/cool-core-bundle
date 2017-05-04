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

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface ValueMapInterface
{
    /**
     * returns the number of admitted values
     * @returns int
     */
    public function getValuesNumber();

    /**
     * maps a value to its display value
     * @param string $value
     * @return string
     */
    public function mapValue($value);

    /**
     * @param string $value
     * @return bool
     */
    public function valueExists($value);

    /**
     * returns an array containing all the values and mapped values
     * in the form ['value'=>'x', 'label'=>'y']
     * if the value map is too big, and/or search criteria are provided, the array may be empty or truncated
     * @param string $value
     * @param string $searchText
     * @param array $parameters Additional parameters
     * @param integer|null $limit
     * @return array
     */
    public function getMap($value = '', $searchText = "", $parameters = [], $limit = null);

    /**
     * returns an array containing all the values
     * @return array
     */
    public function getAllValues();

    /**
     * this method may return the url endpoint of the service that provides dynamic lookup searching in the GUI
     * @return string|null
     */
    public function getAjaxEndPoint();

    /**
     * @param string $ajaxEndPoint
     * @return self
     */
    public function setAjaxEndPoint($ajaxEndPoint);

    /**
     * sets an array of allowed values
     * @param string[] $allowedValues
     * @return self
     */
    public function filterByAllowedValues($allowedValues);

    /**
     * returns a container for parameters
     * @return ParameterBagInterface
     */
    public function getParameters();

}