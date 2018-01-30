<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\DataSource\Interfaces;

use Eulogix\Cool\Lib\DataSource\DataSourceInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface DataSourceHolderInterface {

    /**
     * @param DataSourceInterface $dataSource
     * @return $this
     */
    public function setDataSource($dataSource);

    /**
     * @return DataSourceInterface
     */
    public function getDataSource();

}