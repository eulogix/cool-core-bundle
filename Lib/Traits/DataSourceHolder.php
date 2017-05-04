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

use Eulogix\Cool\Lib\DataSource\DataSourceInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

trait DataSourceHolder {

    /**
     * @var DataSourceInterface
     */
    private $dataSource = null;

    /**
     * @param DataSourceInterface $dataSource
     * @return $this
     */
    public function setDataSource($dataSource)
    {
        $this->dataSource = $dataSource;
        return $this;
    }

    /**
     * @return DataSourceInterface
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

}