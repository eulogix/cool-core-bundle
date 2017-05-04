<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\om\BaseTableExtensionField;
use Eulogix\Cool\Lib\Translation\TranslatorInterface;
use Eulogix\Cool\Lib\Dictionary\Lookup as DictionaryLookup;

class TableExtensionField extends BaseTableExtensionField
{
    /**
     * @param string $schemaName
     * @param TranslatorInterface $translator
     * @return DictionaryLookup
     */
    public function getDictionaryLookup($schemaName, TranslatorInterface $translator) {
        return $this->getFieldDefinition() ? $this->getFieldDefinition()->getDictionaryLookup($schemaName, $translator) : null;
    }
}
