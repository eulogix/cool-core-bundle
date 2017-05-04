<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\CWidget\Core\Lookup;

use Eulogix\Cool\Lib\Lister\CoolLister;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class LookupLister extends CoolLister {
    
    public function build() {
        parent::build();
        return $this;
    } 

}