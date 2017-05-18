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
     * @var CacheShim[]
     */
    private $shims;

    /**
     * @var string
     */
    private $shimUID;

    /**
     * @return CacheShim
     */
    public function getShim() {
        if(!isset($this->shims[$this->getShimUID()]))
            $this->setShim(new CacheShim($this, Cool::getInstance()->getFactory()->getCacher(), $this->getShimUID()));
        return $this->shims[$this->getShimUID()];
    }

    /**
     * @param CacheShim $shim
     */
    public function setShim( $shim ) {
        $this->shims[$this->getShimUID()] = $shim;
    }

    /**
     * @param string $uid
     */
    public function setShimUID( $uid) {
        $this->shimUID = $uid;
    }

    /**
     * @return string
     */
    public function getShimUID() {
        if(!$this->shimUID)
            $this->setShimUID(get_class($this));
        return $this->shimUID;
    }
    
}