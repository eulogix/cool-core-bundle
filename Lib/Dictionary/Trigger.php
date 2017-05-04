<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Dictionary;

use Eulogix\Cool\Lib\DataSource\Bean;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Trigger extends Bean {

    /**
     * @var string
     */
    private $name,
        $language,
        $when,
        $body,
        $raw;

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        $ret = $this->body;
        if($this->getLanguage()=="plpgsql" && !$this->getRaw()) {
            //add code to let the trigger work on the proper schema
            //TODO: implement it for v8 and other pg languages

            $ret = preg_replace('/RETURN ([^;]+?);/sim', "PERFORM set_config('search_path', oldpath, false);\nRETURN $1;", $ret);
            $ret = "DECLARE
                        oldpath text;
                    BEGIN
                        oldpath := current_setting('search_path');
                        PERFORM set_config('search_path', TG_TABLE_SCHEMA, true);
                        {$ret}
                    END";
        }
        return $ret;
    }

    /**
     * @param string $when
     */
    public function setWhen($when)
    {
        $this->when = $when;
    }

    /**
     * @return string
     */
    public function getWhen()
    {
        return $this->when;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $raw
     * @return $this
     */
    public function setRaw($raw)
    {
        $this->raw = $raw;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRaw()
    {
        return $this->raw;
    }
}