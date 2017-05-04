<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\CWidget\Core\User;

use Eulogix\Cool\Lib\Lister\CoolLister;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class UserLister extends CoolLister {
    
    public function build() {
        parent::build();
        return $this;
   }
  
}