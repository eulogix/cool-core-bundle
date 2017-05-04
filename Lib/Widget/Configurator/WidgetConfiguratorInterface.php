<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Widget\Configurator;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface WidgetConfiguratorInterface {
    
    public function load();
    
    public function apply();

    /**
    * @return array
    */
    public function getStoredVariations($variation=null);

    /**
     * returns the Id of the record in the table form_config that perfectly matches a given variation.
     * This is used when saving/loading records in the editor
     * @param string $variation
     * @return integer
     */
    public function getStoredId($variation=null);

    /**
     * returns the Id of the record in the table form_config that best matches the variation configuration:
     * this is used when rendering the form, to pick up the best matching config (if any)
     * @param string $variation
     * @return int
     */
    public function getBestMatchingStoredId($variation=null);

    /**
     * tokenizer for the variation array
     *
     * @param mixed $variation
     * @return mixed
     */
    public function getVariationString($variation=null);
}