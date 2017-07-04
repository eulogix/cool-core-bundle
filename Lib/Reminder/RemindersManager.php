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
use Eulogix\Cool\Lib\Util\DateFunctions;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * This class manages the various notification providers, and is the entry point and collector for all the implementations
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class RemindersManager {

    use ParametersHolder;

    /**
     * @var ReminderProviderInterface[]
     */
    private $providers = [];

    private $providerTranslationDomains = [];

    /**
     * @var string
     */
    private $country;

    /**
     * @param string $uniqueName
     * @param ReminderProviderInterface $provider
     * @param string $translationDomain
     * @return $this
     */
    public function addProvider($uniqueName, ReminderProviderInterface $provider, $translationDomain = null) {
        $this->providers[$uniqueName] = $provider;
        $this->providerTranslationDomains[$uniqueName] = $translationDomain;
        return $this;
    }

    /**
     * @return ReminderProviderInterface[]
     */
    public function getAllProviders() {
        return $this->providers;
    }

    /**
     * @return ReminderProviderInterface[]
     */
    public function getSimpleProviders() {
        return array_filter($this->providers, function($p){
            /** @var ReminderProviderInterface $p */ return $p->getType() == ReminderProviderInterface::TYPE_SIMPLE;
        });
    }

    /**
     * @return ReminderProviderInterface[]
     */
    public function getDatedProviders() {
        return array_filter($this->providers, function($p){
            /** @var ReminderProviderInterface $p */ return $p->getType() == ReminderProviderInterface::TYPE_DATED;
        });
    }

    /**
     * @param $uniqueName
     * @return null|ReminderProviderInterface
     */
    public function getProvider($uniqueName) {
        if(isset($this->providers[$uniqueName]))
            return $this->providers[$uniqueName];
        return null;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * returns an array that counts the dated reminders for $days days, one row for each registered manager
     * @param \DateTime $dateStart
     * @param $days
     * @param bool $includeBoundaries
     * @return array
     */
    public function getDatedCountMatrix($dateStart, $days, $includeBoundaries=true)
    {
        $allCounts = [];
        $retDays = [];



        $dateEnd = clone $dateStart;
        $dateEnd->add(new \DateInterval("P".($days-1)."D"));

        $daysDiff = ($dateEnd->getTimestamp() - $dateStart->getTimestamp())/(3600*24);

        $stopwatch = new Stopwatch();

        foreach($this->getDatedProviders() as $uniqueName => $p) {

            $errors = [];
            $stopwatch->start($uniqueName);

            $p->getParameters()->replace($this->getParameters()->all());

            $dayCounts = [];

            try {
                $before = $includeBoundaries ? $p->countBeforeDate($dateStart) : null;
            } catch(\Exception $e) {
                $before = -1;
                $errors[] = $e->getMessage();
            }

            try {
                $after = $includeBoundaries ? $p->countAfterDate($dateEnd) : null;
            } catch(\Exception $e) {
                $after = -1;
                $errors[] = $e->getMessage();
            }

            for($i=0; $i<=$daysDiff; $i++) {
                $day = clone $dateStart;
                try {
                    $dayCounts[] = $p->countAtDate($day->add(new \DateInterval("P{$i}D")));
                } catch(\Exception $e) {
                    $dayCounts[] = -1;
                    $errors[] = $e->getMessage();
                }
            }

            $event = $stopwatch->stop($uniqueName);

            $allCounts[ $uniqueName ] = [
                'before' => $before,
                'days' => $dayCounts,
                'after' => $after,
                'detailsLister' => $p->getDetailsLister(),
                'detailsTranslationDomain' => $this->providerTranslationDomains[ $uniqueName ],
                'processingDuration' => $event->getDuration(),
                'processingMemoryUsage' => $event->getMemory(),
                'errors' => $errors
            ];
        }

        for($i=0;$i<$days;$i++) {
            $day = clone $dateStart;
            $day = $day->add(new \DateInterval("P{$i}D"));
            $retDays[] = [
                'timestamp'=> $day->getTimestamp(),
                'weekend' => DateFunctions::isWeekend($day),
                'holiday' => DateFunctions::isHoliday($day, $this->getCountry()),
                'today' => DateFunctions::isToday($day)
            ];
        }

        return [
            'counts'=>$allCounts,
            'days'=>$retDays
            ];
    }

    /**
     * returns an array that counts the simple count matrix
     * @return array
     */
    public function getSimpleCountMatrix()
    {
        $allCounts = [];


        $stopwatch = new Stopwatch();

        foreach($this->getSimpleProviders() as $uniqueName => $p) {

            $errors = [];

            $stopwatch->start($uniqueName);

            $p->getParameters()->replace($this->getParameters()->all());

            try {
                $count = $p->countAll();
            } catch(\Exception $e) {
                $count = -1;
                $errors[] = $e->getMessage();
            }

            $event = $stopwatch->stop($uniqueName);

            $allCounts[ $uniqueName ] = [
                'count' => $count,

                'detailsLister' => $p->getDetailsLister(),
                'detailsTranslationDomain' => $this->providerTranslationDomains[ $uniqueName ],

                'processingDuration' => $event->getDuration(),
                'processingMemoryUsage' => $event->getMemory(),
                'errors' => $errors
            ];
        }

        return [
            'counts'=>$allCounts,
        ];
    }

    /**
     * This method, which has to be implemented in the actual implementation of the manager,
     * has the responsibility of setting up and initializing all the providers of the manager.
     * You should also ensure that, even if called multiple times, it performs expensive initializations
     * only once.
     * @return $this
     */
    abstract public function initialize();

}