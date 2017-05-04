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

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\om\BaseFieldDefinition;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Form\Field\Field;
use Eulogix\Cool\Lib\Dictionary\Lookup as DictionaryLookup;
use Eulogix\Cool\Lib\Translation\TranslatorInterface;

class FieldDefinition extends BaseFieldDefinition
{
    /**
     * @return string
     */
    public function getCoolFieldType() {
        switch( $this->getControlType() ) {
            case 'DATE': $controlType = Field::TYPE_DATE; break;
            case 'DATETIME': $controlType = Field::TYPE_DATETIME; break;
            case 'SELECT': $controlType = Field::TYPE_SELECT; break;
            case 'INTEGER': $controlType = Field::TYPE_INTEGER; break;
            case 'DOUBLE': $controlType = Field::TYPE_NUMBER; break;
            case 'CURRENCY': $controlType = Field::TYPE_CURRENCY; break;
            case 'TEXTBOX': $controlType = Field::TYPE_TEXTBOX; break;
            case 'TEXTAREA': $controlType = Field::TYPE_TEXTAREA; break;
            case 'CHECKBOX': $controlType = Field::TYPE_CHECKBOX; break;
            default: $controlType = Field::TYPE_TEXTBOX;
        }
        return $controlType;
    }

    /**
     * @param string $schemaName
     * @param TranslatorInterface $translator
     * @return DictionaryLookup
     */
    public function getDictionaryLookup($schemaName, TranslatorInterface $translator) {
        $d = new DictionaryLookup($schemaName, $translator);
        $d->setType( $this->getLookupType() );

        switch($this->getLookupType()) {
            case 'OTLT' :
            case 'table' : { $d->setDomainName($this->getLookup()); break;}
            case 'enum' : { $d->setValidValues($this->getLookup()); break;}
            case 'FK' : break;
            case 'valueMap' : { $d->setValueMapClass($this->getLookup()); break;}
            case 'valueMapService' : { $d->setValueMapService($this->getLookup()); break; }
        }

        return $d;
    }

}
