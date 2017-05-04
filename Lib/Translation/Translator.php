<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Translation;

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Translation as TModel;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Translator implements TranslatorInterface
{
    /**
    * @var string[]
    */
    private $domains;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var bool
     */
    private $debug = false;

    /**
     * @var string
     */
    private $localeTable = 'translation';

    /**
     * @var bool
     */
    private $expose = false;

    /**
     * @param string $domain
     * @return TranslatorInterface
     */
    public static function fromDomain($domain) {
        $translator = new Translator();
        $translator->setDomains( $domain );
        $translator->setLocale( Cool::getInstance()->getFactory()->getSession()->getLocale() );
        $translator->setDebug( Cool::getInstance()->getFactory()->getSession()->getDebugTranslations() );
        return $translator;
    }

    /**
     * @inheritdoc
     */
    public function setDomains( $domains ) {
        $domainsArray = is_array($domains) ? $domains : [$domains];
        $this->domains = $domainsArray;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addDomain( $domain ) {
        array_push( $this->domains, $domain );
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDomains() {
        return $this->domains;
    }

    /**
     * @inheritdoc
     */
    public function setLocale( $locale ) {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLocale() {
        return $this->locale;
    }

    /**
     * @inheritdoc
     */
    public function setDebug($status=false) {
        $this->debug = $status ? true : false;
        return $this;
    }

    /**
     * @param boolean $expose
     * @return $this
     */
    public function setExpose($expose)
    {
        $this->expose = $expose;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getExpose()
    {
        return $this->expose;
    }


    /**
     * This translator operates like the Symfony one when a single domain is passed, but also accepts an array of domains.
     * In this latter case, it looks for a valid translation in any of them , starting from the first (the most specific), to the last (the default, or fallback)
     * When nothing is found in the database, a new inactive entry is added and returned for the fallback domain
     *                     
     * @param string                    $id         The message id (may also be an object that can be cast to string)
     * @param array                     $parameters An array of parameters for the message
     * @param string|null|string[]      $domain     The domain for the message or null to use the default
     * @param string|null               $locale     The locale or null to use the default
     *
     * @return string The translated string
    */
    public function trans($id, array $parameters = array(), $domain = null, $locale = null) {
        //we build an array of domains, or retrieve the instance ones
        if(!$domain) {
            $domainsArray = $this->getDomains();
        } else {
            $domainsArray = is_array($domain) ? $domain : [$domain];
        }
        
        //same for the locale
        $workLocale = $locale ? $locale : $this->getLocale();
        
        //cycle thru the domains to find an active translation
        $rawTranslation = false;
        foreach($domainsArray as $key=>$d) if(!$rawTranslation) {
            if(($translation = $this->getTranslation($id, $d, $workLocale)) && $translation['active_flag']) {
                $rawTranslation = $translation['value'];
            }    
            if(!$translation && $key==0) {
                //nothing exists in the database for this domain key, so we insert an inactive translation only for the first (main) domain
                //to avoid polluting the translation table with too many inactive entries
                $this->insertTranslation($id, $this->getDefaultValue($id), $d, $workLocale);
            }
        }
        
        //if no active translations have been found, return the default
        $rawTranslation = $rawTranslation ? $rawTranslation : $this->getDefaultValue($id);
        $ret = strtr($rawTranslation, $parameters);
        
        return $this->debug ? "$ret ($id)" : $ret;
    }

    /**
     * @inheritdoc
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null) {
        //TODO?    
    }

    /**
     * @param string $id
     * @param string $value
     * @param string $domain
     * @param string $locale
     * @throws \Exception
     * @throws \PropelException
     */
    private function insertTranslation($id, $value, $domain, $locale) 
    {
        $o = new TModel();
        $o->setDomainName( $domain );
        $o->setLocale( $locale );
        $o->setToken( $id );
        $o->setValue( $value );
        if($this->getExpose())
            $o->setExposeFlag(true);
        $o->save();        
    }

    /**
     * @param string $id
     * @return string
     */
    private function getDefaultValue($id) {
        return $id."[T]";
    }

    /**
     * @param string $id
     * @param string $domain
     * @param string $locale
     * @return array|bool
     */
    private function getTranslation($id, $domain, $locale) {
        $cacher = Cool::getInstance()->getFactory()->getCacher();
        $cacheToken = $cacher->tokenize(func_get_args());
        if($cacher->exists($cacheToken)) {
            $ret = $cacher->fetch($cacheToken);
            if($this->getExpose() && $ret && !$ret['expose_flag']) {
                Cool::getInstance()->getCoreSchema()->query("UPDATE {$this->localeTable} SET expose_flag=TRUE WHERE translation_id=:id", array(":id"=>$ret['translation_id']));
            }
            return $ret;
        }

        $cdb = Cool::getInstance()->getCoreSchema();
        try {
            $item = $cdb->fetch($sql = "SELECT * FROM {$this->localeTable} WHERE domain_name=:domain AND locale=:locale AND token=:token", array(":domain"=>$domain, ":locale"=>$locale, ":token"=>$id));
            if($item && !$item['used_flag']) {
                $cdb->query("UPDATE {$this->localeTable} SET used_flag=TRUE WHERE translation_id=:id", array(":id"=>$item['translation_id']));
            }
            if($item && $this->debug) {
                $cdb->query("UPDATE {$this->localeTable} SET last_usage_date=NOW() WHERE translation_id=:id", array(":id"=>$item['translation_id']));
            }
        } catch(\Exception $e) {
            //table does not exist?
            return array('value'=>$id.'[!!]', 'active_flag'=>1);
        }

        $ret = (!empty($item) ? $item : false);
        $cacher->store($cacheToken, $ret);
        return $ret;
    }

}   