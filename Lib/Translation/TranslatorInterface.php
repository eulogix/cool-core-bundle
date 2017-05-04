<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Translation;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface TranslatorInterface extends \Symfony\Component\Translation\TranslatorInterface
{
    /**
     * @param bool $status
     * @return $this
     */
    public function setDebug($status=false);

    /**
     * @param string[]|string $domains
     * @return $this
     */
    public function setDomains( $domains );

    /**
     * @return string[]
     */
    public function getDomains();

    /**
     * @param string $domain
     * @return $this
     */
    public function addDomain( $domain );    
                        
}   