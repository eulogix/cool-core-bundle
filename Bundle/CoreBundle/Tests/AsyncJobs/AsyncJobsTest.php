<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Tests\AsyncJobs;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJob;
use Eulogix\Cool\Bundle\CoreBundle\Tests\Cases\baseTestCase;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class AsyncJobsTest extends baseTestCase
{
    public function testJob()
    {
        $job = new AsyncJob();

        $job->setIssuerUserId(1)
            ->setExecutorType(AsyncJob::EXECUTOR_RUNDECK)
            ->setJobPath("cool:test:wait")
            ->setParametersArray([
                'seconds' => 3
            ])
            ->save();

        $job->execute();

        sleep(5);

        $job->reload();

        $this->assertGreaterThan(20, $job->getCompletionPercentage());
    }
}