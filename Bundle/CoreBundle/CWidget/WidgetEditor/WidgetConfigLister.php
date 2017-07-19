<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\CWidget\WidgetEditor;

use Eulogix\Cool\Lib\Lister\Lister;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class WidgetConfigLister extends Lister {

    /**
     * @inheritdoc
     */
    public function build() {
        parent::build();
        $this->addAction('new Variation')->setOnClick("widget.openNewRecordEditor();");
        return $this;
    }

    public function getEditedWidgetCurrentVariation() {
        return $this->getParameters()->get('_widgetCurrentVariation');
    }


    public function getRowMeta(array $row) {

        $meta = [];

        if(@$row['variation'] == $this->getEditedWidgetCurrentVariation())
            $meta = [
                self::ROW_META_ROWCLASS         => "gridxRowHighlightBlue",
            ];

        return array_merge(parent::getRowMeta($row), $meta);
    }

}