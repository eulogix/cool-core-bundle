<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Activiti\dataSource;

use Eulogix\Lib\Activiti\ActivitiClient;
use Eulogix\Cool\Lib\DataSource\BaseDataSource;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class ActivitiDataSource extends BaseDataSource {

    /**
     * @var ActivitiClient
     */
    protected $client;

    public function __construct(ActivitiClient $client) {
        $this->setClient($client);
    }

    /**
     * @param ActivitiClient $client
     * @return $this
     */
    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return ActivitiClient
     */
    public function getClient()
    {
        return $this->client;
    }
}