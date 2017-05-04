<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Tests\Cases;

use Eulogix\Cool\Lib\Cool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class baseTestCase extends KernelTestCase
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    protected function setUp()
    {
        parent::setUp();
        self::bootKernel();
        Cool::getInstance()->initSchemas();
        \Propel::disableInstancePooling();
    }

}
