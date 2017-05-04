<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\DataSource\Renderer;

use Eulogix\Cool\Lib\Lister\Column;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface RendererInterface {

    /**
     * $rows are rows as returned by DS query operations
     * return value may be text, or the binary content of a file
     * @param Column[] $listerColumnsDefinitions
     * @param array $rows
     * @param bool $raw
     * @internal param array $columns
     * @return mixed
     */
    public function renderData(array $rows, $raw, array $listerColumnsDefinitions=null);

} 