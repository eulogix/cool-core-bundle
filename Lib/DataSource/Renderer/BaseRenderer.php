<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\DataSource\Renderer;

use Eulogix\Cool\Lib\DataSource\DataSourceInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class BaseRenderer {

    /**
     * @param array $data
     * @return array
     */
    protected function getDecodedRows(array $data) {
        $ret = [];
        foreach($data as $row)
            $ret[] = $row[DataSourceInterface::DECODIFICATIONS_IDENTIFIER];
        return $ret;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getRawRows(array $data) {
        $ret = $data;
        foreach($ret as &$row) {
            unset( $row[DataSourceInterface::RECORD_IDENTIFIER] );
            unset( $row[DataSourceInterface::DECODIFICATIONS_IDENTIFIER] );
        }
        return $ret;
    }

    /**
     * returns a filtered array of rows with only the columns specified in $columnNames, in the same order
     * @param array $data
     * @param string[] $columnNames
     * @return array
     */
    protected function filterData(array $data, array $columnNames) {
        return array_map(function($row) use($columnNames) {
            $ret = [DataSourceInterface::DECODIFICATIONS_IDENTIFIER => []];
            foreach($columnNames as $c) {
                $ret[$c] = @$row[$c];
                $ret[DataSourceInterface::DECODIFICATIONS_IDENTIFIER][$c] = @$row[DataSourceInterface::DECODIFICATIONS_IDENTIFIER][$c];;
            }
            return $ret;
        }, $data);
    }

}