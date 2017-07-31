<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\CWidget\Workflows;

use Eulogix\Cool\Lib\Widget\Widget;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Explorer extends Widget {

    public static function getClientWidget()
    {
        return "cool/workflow/explorer";
    }

    public function build() {
        $this->getAttributes()->set(self::ATTRIBUTE_HIDE_TOOLBAR, true);
        return parent::build();
    }
}