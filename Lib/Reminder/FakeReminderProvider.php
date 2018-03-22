<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Reminder;

use Eulogix\Cool\Lib\Traits\ParametersHolder;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FakeReminderProvider implements ReminderProviderInterface
{

    use ParametersHolder;

    /**
     * @inheritdoc
     */
    public function countAtDate(\DateTime $date)
    {
        return $this->superFakeNumber();
    }

    /**
     * @inheritdoc
     */
    public function countBeforeDate(\DateTime $date)
    {
        return $this->superFakeNumber();
    }

    /**
     * @inheritdoc
     */
    public function countAfterDate(\DateTime $date)
    {
        return $this->superFakeNumber();
    }

    /**
     * @inheritdoc
     */
    public function getDetailsDataSource()
    {
        // TODO: Implement getDetailsDataSource() method.
    }

    /**
     * @inheritdoc
     */
    public function getDetailsLister()
    {
        return "gino";
    }

    private function superFakeNumber() {
        return rand(0,$this->getParameter('max'));
    }

    /**
     * @return ParameterBag
     */
    public function getParameters()
    {
        // TODO: Implement getParameters() method.
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        // TODO: Implement getType() method.
    }

    /**
     * @inheritdoc
     */
    public function getCategory()
    {
        // TODO: Implement getCategory() method.
    }

    /**
     * @inheritdoc
     */
    public function countAll()
    {
        // TODO: Implement countAll() method.
    }
}