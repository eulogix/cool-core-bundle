<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Rundeck;

use Eulogix\Lib\Rundeck\SymfonyUtils;
use Eulogix\Cool\Lib\Symfony\Console\CoolCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CoolRDSymfonyUtils extends SymfonyUtils {

    /**
     * @inheritdoc
     */
    protected function getJobOptionFromInputArgument(Command $command, InputArgument $arg)
    {
        $jobOption = parent::getJobOptionFromInputArgument($command, $arg);

        if ($command instanceof CoolCommand) {
            if ($rx = $command->getRegex($arg->getName())) {
                $jobOption->setRegex($rx);
            }
            if ($vl = $command->getValidValues($arg->getName())) {
                $jobOption->setAllowedValues($vl);
            }
            if ($command->isMultiValued($arg->getName()))
                $jobOption->setIsMultiValued(true)->setEnforcedValues(true);
        }

        return $jobOption;
    }

    /**
     * @inheritdoc
     */
    protected function getJobOptionFromInputOption(Command $command, InputOption $option)
    {
        $jobOption = parent::getJobOptionFromInputOption($command, $option);

        if ($command instanceof CoolCommand) {
            if ($rx = $command->getRegex($option->getName())) {
                $jobOption->setRegex($rx);
            }
            if ($vl = $command->getValidValues($option->getName())) {
                $jobOption->setAllowedValues($vl);
            }
            if ($command->isMultiValued($option->getName()))
                $jobOption->setIsMultiValued(true)->setEnforcedValues(true);
        }

        return $jobOption;
    }

} 