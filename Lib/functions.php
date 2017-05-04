<?php
/**
 * mb aware version of pathinfo
 * @param $filepath
 * @return mixed
 */

function mb_pathinfo($filepath) {
    if($e=mb_detect_encoding($filepath)=='ASCII') return pathinfo($filepath);
    preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im',$filepath,$m);
    if($m[1]) $ret['dirname']=$m[1];
    if($m[2]) $ret['basename']=$m[2];
    if(@$m[5]) $ret['extension']=$m[5];
    if($m[3]) $ret['filename']=$m[3];
    return $ret;
}



function c_query($sql, $con) {
    $sth = $con->prepare($sql);
    $sth->execute();
    return $sth;
}

 function c_fetch($sql, $con) {
    $sth = c_query($sql, $con);
    $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
    //if we issued a query like select count(*) from...the raw value is returned
    if( $sth->columnCount() == 1 && $sth->rowCount() == 1)
        $ret = array_pop($result[0]);
    //if we have only 1 record returned, we return it instead of an array of arrays
    elseif( $sth->rowCount() == 1)
        $ret = $result[0];
    else $ret = $result;

    return $ret;
}


/**
 * tells wether a certain class, namespace, or portion of it appears in the backtrace
 * FALSE means nothing found, otherwise the function returns the index
 * @param $string
 * @return int|bool
 */
function backtrace_search($string) {
    $dt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS && DEBUG_BACKTRACE_PROVIDE_OBJECT);
    foreach($dt as $index => $trace) {
        if(strpos($trace['file'], $string)!==false)
            return $index;
    }
    return false;
}

/*
$data[] = array('volume' => 86, 'edition' => 6);
$data[] = array('volume' => 67, 'edition' => 7);

// Pass the array, followed by the column names and sort flags
$sorted = array_orderby($data, 'volume', SORT_DESC, 'edition', SORT_ASC);
*/
function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = @$row[$field];
            $args[$n] = $tmp;
        }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}

/**
 * @param array $array ['foo'=>'123', 'bar'=>'239', 'baz'=>333]
 * @param string[] $allowedKeys ['foo','baz']
 * @return array ['foo'=>'123', 'baz'=>333]
 */
function array_filter_allowed_keys($array, $allowedKeys) {
    $ret = array_intersect_key($array, array_flip($allowedKeys));
    return $ret;
}

/**
 * @param $array
 * @param string $allowedKeys
 * @return mixed
 */
function array_filter_allowed_keys_table($array, $allowedKeys) {
    $workArray = $array;
    foreach($workArray as &$row)
        $row = array_filter_allowed_keys($row, $allowedKeys);
    return $workArray;
}

/**
 * xml2array that always normalizes children to arrays so that even if there is 1 element, an array is returned
 * @param SimpleXMLElement $xml
 * @return array|mixed
 */
function SimpleXMLElement2array(SimpleXMLElement $xml)
{
    $arr = [];
    if($attrs = $xml->attributes()) {
        $arr = json_decode(json_encode($attrs),1);
    }

    foreach ($xml->children() as $r)
    {
        if(count($r->children()) == 0)
        {
            $arr[$r->getName()] = strval($r);
        }
        else
        {
            $arr[$r->getName()][] = SimpleXMLElement2array($r);
        }
    }

    return $arr;
}

function url_origin($s, $use_forwarded_host=false)
{
    $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true:false;
    $sp = strtolower($s['SERVER_PROTOCOL']);
    $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
    $port = $s['SERVER_PORT'];
    $port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
    $host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
    $host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
    return $protocol . '://' . $host;
}

function full_url($s, $use_forwarded_host=false)
{
    return url_origin($s, $use_forwarded_host) . $s['REQUEST_URI'];
}

/**
 * Cast an object to another class, keeping the properties, but changing the methods
 *
 * @param string $class  Class name
 * @param object $object
 * @return object
 */
function castToClass($class, $object)
{
    file_put_contents('/opt/webapps/log.txt', "." , FILE_APPEND | LOCK_EX);
    $ser = serialize($object);
    $modSer = preg_replace('/^O:\d+:"[^"]++"/', 'O:' . strlen($class) . ':"' . $class . '"', $ser);
    $ret = unserialize($modSer);
    return $ret;
}