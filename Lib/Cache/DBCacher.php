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
 * Simple temporary key value store that can be used across CLI and WEB, uses a table in a schema
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class DBCacher implements CacherInterface
{
    /**
     * @var string
     */
    private $schema, $table;

    /**
     * @param string $table
     * @param string $schema
     */
    function __construct($table = 'main_cacher', $schema = 'temp') {
        $this->table = $table;
        $this->schema = $schema;
        $this->cleanExpired();
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
        return Cool::getInstance()->getCoreSchema()->fetch("SELECT COUNT(*) FROM {$this->schema}.{$this->table} WHERE c_key=:key", [':key'=> $this->keyToInt($key) ]) == 1;
    }

    /**
     * @inheritdoc
     */
    function store($key, $value, $ttlsecs=600) {
        $db = Cool::getInstance()->getCoreSchema();
        $this->delete($key);
        $db->query("INSERT INTO {$this->schema}.{$this->table} (c_key, c_value, expiration_date) VALUES(:key, :value, now()+INTERVAL '$ttlsecs seconds')", [':key'=>$this->keyToInt($key), ':value'=>json_encode($value)]);
        return true;
    }

    /**
     * @inheritdoc
     */
    function fetch($key) {
        return json_decode( Cool::getInstance()->getCoreSchema()->fetch("SELECT c_value FROM {$this->schema}.{$this->table} WHERE c_key=:key", [':key'=>$this->keyToInt($key)]), true);
    }

    /**
     * @inheritdoc
     */
    function delete($key) {
        Cool::getInstance()->getCoreSchema()->query("DELETE FROM {$this->schema}.{$this->table} WHERE c_key=:key", [':key'=>$this->keyToInt($key)]);
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

    private function keyToInt($key) {
        return crc32($key);
    }

    private function cleanExpired() {
        //Cool::getInstance()->getCoreSchema()->query("DELETE FROM {$this->schema}.{$this->table} WHERE expiration_date < NOW()");
    }
}
