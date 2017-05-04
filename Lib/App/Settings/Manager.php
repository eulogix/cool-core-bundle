<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\App\Settings;

use Eulogix\Cool\Lib\Cool;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Manager {

    /**
     * @var int
     */
    private $cacheDurationSecs;

    function __construct($cacheDurationSecs=0)
    {
        $this->cacheDurationSecs = $cacheDurationSecs;
    }

    /**
     * @param string $name
     * @param string $space
     * @return string
     */
    public function getSetting($name, $space=null) {
        $sql = "SELECT value FROM app_setting WHERE name=:name AND ".
            ($space ? "space=:space" : "space IS NULL");

        return Cool::getInstance()->getCoreSchema()->fetch($sql, [":name"=>$name, ":space"=>$space], true, $this->cacheDurationSecs);
    }

} 