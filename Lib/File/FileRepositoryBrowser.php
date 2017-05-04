<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\File;

use Eulogix\Cool\Lib\Widget\Widget;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FileRepositoryBrowser extends Widget {

    protected $type = "form";

    public static function getClientWidget() {
        return 'cool/fileRepoBrowser';
    }

}