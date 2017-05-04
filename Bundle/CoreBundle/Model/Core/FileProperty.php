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

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\om\BaseFileProperty;
use Eulogix\Cool\Lib\File\FileProperty as CoolFileProperty;
use Eulogix\Cool\Lib\Translation\Translator;

class FileProperty extends BaseFileProperty
{
    /**
     * @return CoolFileProperty
     */
    public function getCoolFileProperty() {

        $fd = $this->getFieldDefinition();

        $token = 'COOL_LOOKUPS_'.$this->getContextSchema();
        if($table = $this->getContextTable())
            $token.='_'.$table;
        if($cat = $this->getContextCategory())
            $token.='_'.$cat;

        $translator = Translator::fromDomain( strtoupper($token) );

        $fp = new CoolFileProperty(
            $fd->getName(),
            $fd->getCoolFieldType(),
            $this->getShowInListFlag(),
            $fd->getDictionaryLookup($this->getContextSchema(), $translator)->getValueMap()
        );

        return $fp;
    }
}
