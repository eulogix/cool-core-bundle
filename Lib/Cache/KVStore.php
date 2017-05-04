<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Cache;

use Eulogix\Cool\Lib\Cool;
use Eulogix\Lib\Cache\CacherInterface;

/**
 * simple temporary key value store that can be used across CLI and WEB, uses a table in a schema
 * this KVStore is permanent and uses plain text as the key
 *
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class KVStore implements CacherInterface
{
    /**
     * @var string
     */
    private $schema, $table;

    function __construct($table = 'kv_store', $schema = 'core') {
        $this->table = $table;
        $this->schema = $schema;
    }

    /**
     * @inheritdoc
     */
    function tokenize($variable) {
        return sha1(serialize($variable));
    }

    /**
     * @inheritdoc
     */
    function exists($key) {
        return Cool::getInstance()->getCoreSchema()->fetch("SELECT COUNT(*) FROM {$this->schema}.{$this->table} WHERE c_key=:key", [':key'=> $key ]) == 1;
    }

    /**
     * @inheritdoc
     */
    function store($key, $value, $ttlsecs=600) {
        $db = Cool::getInstance()->getCoreSchema();
        $this->delete($key);
        $db->query("INSERT INTO {$this->schema}.{$this->table} (c_key, c_value) VALUES(:key, :value)", [':key'=>$key, ':value'=>json_encode($value)]);
        return true;
    }

    /**
     * @inheritdoc
     */
    function fetch($key) {
        return json_decode( Cool::getInstance()->getCoreSchema()->fetch("SELECT c_value FROM {$this->schema}.{$this->table} WHERE c_key=:key", [':key'=>$key]), true);
    }

    /**
     * @inheritdoc
     */
    function delete($key) {
        Cool::getInstance()->getCoreSchema()->query("DELETE FROM {$this->schema}.{$this->table} WHERE c_key=:key", [':key'=>$key]);
        return true;
    }

    /**
     * @return boolean
     */
    function flushAll()
    {
        Cool::getInstance()->getCoreSchema()->query("DELETE FROM {$this->schema}.{$this->table}");
        return true;
    }

}
