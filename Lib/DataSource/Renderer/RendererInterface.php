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

use Eulogix\Cool\Lib\DataSource\DSRequest;
use Eulogix\Cool\Lib\DataSource\Interfaces\DataSourceHolderInterface;
use Eulogix\Cool\Lib\Lister\Column;
use Eulogix\Lib\Progress\ProgressTracker;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface RendererInterface extends DataSourceHolderInterface {

    /**
     * @return ProgressTracker
     */
    public function getProgressTracker();

    /**
     * $rows are rows as returned by DS query operations
     * return value may be text, or the binary content of a file
     * @param array $rows
     * @param bool $raw
     * @param Column[] $listerColumnsDefinitions
     * @return mixed
     */
    public function renderData(array $rows, $raw, array $listerColumnsDefinitions=null);

    /**
     * @param DSRequest $request
     * @param bool $raw
     * @param Column[] $listerColumnsDefinitions
     * @param int $asyncIfMoreThanRows
     * @return mixed
     */
    public function render(DSRequest $request, $raw = false, array $listerColumnsDefinitions = null, $asyncIfMoreThanRows = null);
} 