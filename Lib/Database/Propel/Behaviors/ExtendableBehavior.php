<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Database\Propel\Behaviors;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ExtendableBehavior extends \Behavior
{
    protected $parameters = array(
        'container_column'          => 'ext',
    );

    public function modifyTable()
    {
        if (!$this->getTable()->hasColumn($this->getParameter('container_column'))) {
            $this->getTable()->addColumn(array(
                'name' => $this->getParameter('container_column'),
                'type' => 'LONGVARCHAR',
                'sqlType' => 'json'
            ));
        }
    }
}
