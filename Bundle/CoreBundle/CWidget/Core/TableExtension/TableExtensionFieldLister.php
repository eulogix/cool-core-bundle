<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\CWidget\Core\TableExtension;

use Eulogix\Cool\Lib\DataSource\Classes\TableExtension\TableExtensionFieldDataSource;
use Eulogix\Cool\Lib\Lister\Lister;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class TableExtensionFieldLister extends Lister {

    public function __construct($parameters = [])
    {
        parent::__construct($parameters);
        $ds = new TableExtensionFieldDataSource();
        $this->setDataSource($ds->build());
    }

    public function build() {
        parent::build();
        $this->addAction('new Field')->setOnClick("widget.openNewRecordEditor();");
        return $this;
    }

    public function getDefaultEditorServerId() {
        return 'EulogixCoolCore/Core/TableExtension/TableExtensionFieldEditorForm';
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return "COOL_TABLE_EXTENSION_FIELD_LISTER";
    }

}