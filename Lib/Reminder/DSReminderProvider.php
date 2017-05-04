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

use Eulogix\Cool\Lib\DataSource\DataSourceInterface;
use Eulogix\Cool\Lib\DataSource\DSRequest;
use Eulogix\Cool\Lib\Traits\DataSourceHolder;
use Eulogix\Cool\Lib\Traits\ParametersHolder;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class DSReminderProvider implements ReminderProviderInterface {

    const ISODATE = 'isoDate';
    const COMPARISON = 'comparison';

    const COMPARISON_EQUAL = 'equal';
    const COMPARISON_GREATER = 'greater';
    const COMPARISON_SMALLER = 'smaller';

    /**
     * @var string
     */
    protected $detailsLister, $type;

    use ParametersHolder;

    use DataSourceHolder;

    public function __construct(DataSourceInterface $dataSource, $parametersArray=[]) {
        $this->setDataSource($dataSource);
        $this->getParameters()->replace($parametersArray);
        $this->setDetailsLister('Eulogix/Cool/Lib/Reminder/RemindersLister');
    }

    /**
     * @inheritdoc
     */
    public function countAll()
    {
        $dsr = new DSRequest();

        $dsr->setOperationType($dsr::OPERATION_TYPE_COUNT)
            ->setParameters( $this->getDSRequestParameters() );
        $dsresponse = $this->getDataSource()->execute($dsr);
        return $dsresponse->getTotalRows();
    }

    /**
     * @inheritdoc
     */
    public function countAtDate(\DateTime $date)
    {
        $dsr = new DSRequest();

        $dsr->setOperationType($dsr::OPERATION_TYPE_COUNT)
            ->setParameters( $this->getDSRequestParameters( [self::ISODATE => $date->format('Y-m-d'), self::COMPARISON => self::COMPARISON_EQUAL] ) );

        $dsresponse = $this->getDataSource()->execute($dsr);
        return $dsresponse->getTotalRows();
    }

    /**
     * @inheritdoc
     */
    public function countBeforeDate(\DateTime $date)
    {
        $dsr = new DSRequest();

        $dsr->setOperationType($dsr::OPERATION_TYPE_COUNT)
            ->setParameters( $this->getDSRequestParameters([self::ISODATE => $date->format('c'), self::COMPARISON => self::COMPARISON_SMALLER]) );

        $dsresponse = $this->getDataSource()->execute($dsr);
        return $dsresponse->getTotalRows();
    }

    /**
     * @inheritdoc
     */
    public function countAfterDate(\DateTime $date)
    {
        $dsr = new DSRequest();

        $dsr->setOperationType($dsr::OPERATION_TYPE_COUNT)
            ->setParameters( $this->getDSRequestParameters([self::ISODATE => $date->format('c'), self::COMPARISON => self::COMPARISON_GREATER]) );

        $dsresponse = $this->getDataSource()->execute($dsr);
        return $dsresponse->getTotalRows();
    }

    /**
     * @inheritdoc
     */
    public function getDetailsDataSource()
    {
        return $this->getDataSource();
    }

    /**
     * @inheritdoc
     */
    public function getDetailsLister()
    {
        return $this->detailsLister;
    }

    /**
     * @param string $detailsLister
     * @return $this
     */
    public function setDetailsLister($detailsLister)
    {
        $this->detailsLister = $detailsLister;
        return $this;
    }

    /**
     * @param array $parameters
     * @return array
     */
    private function getDSRequestParameters(array $parameters=[]) {
        return array_merge($this->getParameters()->all(), $parameters);
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
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

}