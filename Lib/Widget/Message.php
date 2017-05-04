<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Widget;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Message {
    const TYPE_INFO         =    'info';
    const TYPE_WARNING      =    'warning';
    const TYPE_ERROR        =    'error';

    /**
     * @var string
     */
    public  $type;

    /**
     * @var string
     */
    private $text = "";

    /**
     * @param string $type
     * @param string $text
     */
    public function __construct($type, $text=null)
    {
        $this->type = $type;
        if($text)
            $this->setText($text);
    }

    /**
     * Adds a message, {1}...{n} may be used as placeholders for text variables
     * @param mixed $token,...
     *
     * @return $this
     */
    public function setText($token) 
    {
        $mappedString = $this->mapToken($token);
        if(func_num_args()>1) {
            for($i=1; $i<func_num_args(); $i++) {
                $mappedString = preg_replace('/(\{'.$i.'\})/im', func_get_arg($i), $mappedString);    
            } 
        }
        $this->text = $mappedString;
        return $this;
    }

    /**
     * maps a text token with the actual message that the user sees, usually by means of translation
     *
     * @param string $token
     * @return string
     */
    private function mapToken($token) {
        return $token;
    }
    
    /**
    * returns the definition of the message
    * @return array
    */
    public function getDefinition() {
         $def = array(
            "type"=>$this->type,
            "text"=>$this->text,
         );
         return $def;
    }

}