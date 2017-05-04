<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Session;

use Eulogix\Cool\Lib\Cool;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CoolSession extends Session {
    
    const ATT_LOCALE        = '_locale';
    const ATT_DEBUG_MODE    = '_cool_debug_mode';
    const ATT_DEBUG_LOOKUPS    = '_cool_debug_lookups';
    const ATT_DEBUG_TRANSLATIONS    = '_cool_debug_translations';

    /**
    * @param string $locale
    * @return $this
    */
    public function setLocale($locale) {
        $this->set(self::ATT_LOCALE, $locale);
        return $this;
    }

    /**
     * @return string
     */
    public function getLocale() {
        $ret = $this->get(self::ATT_LOCALE);
        if(!$ret)
            $this->setLocale( $ret = Cool::getInstance()->getContainer()->getParameter('kernel.default_locale') );
        return $ret;
    }
    
    /**
    * sets the debug mode (for widget inspection)
    * 
    * @param boolean $debugMode
    * @return CoolSession
    */
    public function setDebugMode( $debugMode ) {
        $this->set(self::ATT_DEBUG_MODE, $debugMode);
        return $this;
    }
    
    /**
    * @return boolean
    */
    public function getDebugMode() {
        return $this->get(self::ATT_DEBUG_MODE);
    }

    /**
    * sets the debug translations
    * 
    * @param boolean $debugTranslations
    * @return CoolSession
    */
    public function setDebugTranslations( $debugTranslations ) {
        $this->set(self::ATT_DEBUG_TRANSLATIONS, $debugTranslations);
        return $this;
    }
    
    /**
    * @return boolean
    */
    public function getDebugTranslations() {
        return $this->get(self::ATT_DEBUG_TRANSLATIONS);
    }
    
    /**
    * sets the debug lookups
    * 
    * @param boolean $debugLookups
    * @return CoolSession
    */
    public function setDebugLookups( $debugLookups ) {
        $this->set(self::ATT_DEBUG_LOOKUPS, $debugLookups);
        return $this;
    }
    
    /**
    * @return boolean
    */
    public function getDebugLookups() {
        return $this->get(self::ATT_DEBUG_LOOKUPS);
    }

}