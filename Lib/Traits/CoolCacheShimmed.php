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

use Eulogix\Lib\Cache\CacheShim;
use Eulogix\Cool\Lib\Cool;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

trait CoolCacheShimmed {

    /**
     * @var CacheShim
     */
    public $shim;

    /**
     * @return CacheShim
     */
    public function getShim() {
        if(!$this->shim)
            $this->setShim(new CacheShim($this, Cool::getInstance()->getFactory()->getCacher(), $this->getShimUID()));
        return $this->shim;
    }

    /**
     * @param CacheShim $shim
     */
    public function setShim( $shim ) {
        $this->shim = $shim;
    }

    /**
     * @return string
     */
    public function getShimUID() {
        return get_class($this);
    }
    
}