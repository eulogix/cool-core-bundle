<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle;

use Eulogix\Cool\Lib\Widget\Message;
use Symfony\Bridge\Propel1\DependencyInjection\Security\UserProvider\PropelFactory;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Eulogix\Cool\Lib\Cool;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class EulogixCoolCoreBundle extends Bundle
{
    /**         
     * {@inheritdoc}
     */
    public function boot()
    {
        if (!\Propel::isInit()) {
            throw new \Exception("Propel must be initialized by PropelBundle. Register CoolCoreBundle after it in AppKernel");
        } else {
            //when using PHP-PM the instance pool would have to be cleared whenever setCurrentSchema() is called in a MT
            //schema. For now we just disable it
            \Propel::disableInstancePooling();
             
            Cool::getInstance()->setContainer($this->container);
            Cool::getInstance()->initSchemas();
            Cool::getInstance()->refreshSearchPaths();
        }
    }
    
}
