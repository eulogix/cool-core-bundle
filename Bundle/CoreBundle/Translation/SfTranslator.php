<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Translation;

use Symfony\Bundle\FrameworkBundle\Translation\Translator as BaseTranslator;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class SfTranslator extends BaseTranslator {
        
    public function trans($id, array $parameters = array(), $domain = 'messages', $locale = null) {

        if ($locale === null)
            $locale = $this->getLocale();

        if($domain === null)
            $domain = 'messages';

        $CoolTranslator = $this->container->get('cool.translator');
        return $CoolTranslator->trans($id, $parameters, $domain, $locale);
    }

    //TODO: override transChoice ?

}
