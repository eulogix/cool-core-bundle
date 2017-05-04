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

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FileCommand {

    const TYPE_REMOVE_STORED = 'removeStoredFile';

    /**
     * @var string
     */
    protected $type;

    public function __construct($type) {
        $this->setType($type);
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

} 