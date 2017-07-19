<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\DataSource\Classes\WidgetEditor;

use Eulogix\Cool\Lib\DataSource\CoolCrudDataSource as CD;
use Eulogix\Cool\Lib\DataSource\CoolCrudTableRelation as Rel;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FormConfigDataSource extends CD {

    public function __construct()
    {
        $dsTables = [
            CD::PARAM_TABLE_RELATIONS=>[

                Rel::build()
                    ->setTable('core.form_config')
                    ->setDeleteFlag(true)
                    ->setIsRequired(true)
            ]
        ];

        return parent::__construct('core', $dsTables);
    }
}