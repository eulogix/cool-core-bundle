<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\CWidget\Core\FileProperty;

use Eulogix\Cool\Lib\DataSource\Classes\FileProperties\FilePropertyDataSource;
use Eulogix\Cool\Lib\Lister\Lister;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FilePropertiesLister extends Lister {

    public function __construct($parameters = [])
    {
        parent::__construct($parameters);
        $ds = new FilePropertyDataSource();
        $this->setDataSource($ds->build());
    }

    public function build() {
        parent::build();
        $this->addAction('new Property')->setOnClick("widget.openNewRecordEditor();");
        return $this;
    }

    public function getDefaultEditorServerId() {
        return 'EulogixCoolCore/Core/FileProperty/FilePropertyEditorForm';
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return "COOL_FILE_PROPERTIES_LISTER";
    }

}