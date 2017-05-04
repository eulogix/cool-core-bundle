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

use Eulogix\Cool\Lib\Symfony\Console\CoolCommand;
use Eulogix\Lib\Rundeck\JobOption;
use Eulogix\Lib\Rundeck\RundeckJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class SymfonyUtils {

    /**
     * @var string
     */
    private $commandUser, $appPath;

    public function __construct($commandUser, $appPath) {
        $this->commandUser = $commandUser;
        $this->appPath = $appPath;
    }

    /**
     * @param Command $command
     * @param bool $includeDefaultOptions
     * @return RundeckJob
     */
    public function getRundeckJob(Command $command, $includeDefaultOptions=false) {

        $j = new RundeckJob();
        $j->setId($this->getUUID($command));
        $j->setName( $command->getName() );
        $j->setDescription( $command->getDescription() );

        $setupScript = '';
        $scriptContent = "cd {$this->appPath}\ncmd_string=\"sudo -u {$this->commandUser} php console {$command->getName()}";

        $args = $command->getDefinition()->getArguments();
        foreach($args as $arg)
            if($arg->getName()!='command') {
                $option = new JobOption();
                $option->setName($arg->getName());
                $option->setDescription($arg->getDescription());
                $option->setDefaultValue($arg->getDefault());
                $option->setRequired($arg->isRequired());

                if($command instanceof CoolCommand) {
                    if($rx = $command->getRegex($arg->getName()))
                        $option->setRegex($rx);
                    if($vl = $command->getValidValues($arg->getName()))
                        $option->setAllowedValues($vl);
                    if($command->isMultiValued($arg->getName()))
                        $option->setIsMultiValued(true)->setEnforcedValues(true);
                }

                $j->addOption($option);

                $scriptContent.=" ".$this->quoteBashVariable($option->getBashPlaceHolder());
            }

        $options = $command->getDefinition()->getOptions();
        array_push($options, new InputOption('env', null, InputOption::VALUE_OPTIONAL, 'Symfony env'));

        foreach($options as $opt)
            if(!$this->isOptionDefault($opt->getName()) || $includeDefaultOptions) {
                $option = new JobOption();
                $option->setName($opt->getName());
                $option->setDescription($opt->getDescription());
                $option->setDefaultValue($opt->getDefault());
                $option->setRequired($opt->isValueRequired());

                if($command instanceof CoolCommand) {
                    if($rx = $command->getRegex($opt->getName()))
                        $option->setRegex($rx);
                    if($vl = $command->getValidValues($opt->getName()))
                        $option->setAllowedValues($vl);
                    if($command->isMultiValued($opt->getName()))
                        $option->setIsMultiValued(true)->setEnforcedValues(true);
                }

                $j->addOption($option);


                if($opt->isValueRequired()) {
                    $scriptContent.=" --{$opt->getName()} ".$this->quoteBashVariable($option->getBashPlaceHolder());
                } else {

                    $varName = 'opt_'.preg_replace('/[^a-zA-Z]/sim', '', $opt->getName());
                    $setupScript.="
{$varName}=''
if [ \"{$option->getBashPlaceHolder()}\" != '' ]; then
    {$varName}=\"--{$opt->getName()} ".($opt->acceptValue() ? $this->quoteBashVariable($option->getBashPlaceHolder()) : '')."\"
fi\necho \${$varName}\n";
                    $scriptContent.=" \${$varName}";
                }
            }

        $j->setScriptContent( $setupScript."\n$scriptContent\"
            echo \$cmd_string
            eval \$cmd_string
            ret_code=\$?
            echo \$ret_code
            exit \$ret_code" );

        return $j;
    }

    /**
     * @param string $var
     * @return string
     */
    private function quoteBashVariable($var) {
        return "\\\"\\{$var}\\\"";
    }

    /**
     * @param Command $command
     * @return int
     */
    private function getUUID(Command $command) {
        return crc32($command->getName());
    }

    /**
     * @param string $option
     * @return bool
     */
    private function isOptionDefault($option) {
        return in_array($option, [
                'help',
                'quiet',
                'verbose',
                'version',
                'ansi',
                'no-ansi',
                'no-interaction',
                'no-debug',
                //'env',
                'process-isolation',
                'shell']);
    }
} 