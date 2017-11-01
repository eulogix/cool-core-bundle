<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\AsyncJob;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJob;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class BaseExecutor
{
    /**
     * @var AsyncJob
     */
    private $job;

    public function __construct(AsyncJob $job)
    {
        $this->job = $job;
    }

    /**
     * @return AsyncJob
     */
    protected function getJob() {
        return $this->job;
    }

    /**
     * @param bool $updateJob if set, will update and save the async job record
     */
    public abstract function execute($updateJob = true);

}