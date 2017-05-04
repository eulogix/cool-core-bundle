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

class Definition {
    
    private $def = [];
    
    /**
    * the hashes that the client already has
    * @var mixed
    */
    private $clientHashes = [];
    
    public function __construct( $serHash ) {
        $this->clientHashes = unserialize($serHash);
    }

    /**
     * @param string $name
     * @param mixed $block
     */
    public function setBlock($name, $block) {
        $this->def[$name] = $block;        
    }

    /**
     * @return array
     */
    public function getResponse() {
        $hashes = [];
        $retDef = [];
        
        //serve only the elements that have changed since the last call from the client
        foreach($this->def as $key=>$arr) {
            $hashes[$key] = substr(md5(serialize($arr)), 0,5);
            if( in_array($key, ['messages','events','commands','clientParameters']) ||     //these must always be passed along
                !isset($this->clientHashes[$key]) || 
                ($hashes[$key] != $this->clientHashes[$key])) {
                $retDef[$key] = $arr;
            }
        }
        
        return array('_definition'=>$retDef, '_hashes'=>serialize($hashes));
    }
    
}