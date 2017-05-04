<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Util;

use Money\Money;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class DataFormatter
{
    /**
     * @param mixed $value
     * @return string
     */
    public static function formatCurrency($value) {
        if($value instanceof Money) {
            return self::formatCurrency( $value->toDecimal() );
        }
        return number_format($value, 2, ',', '.');
    }

    /**
     * @param mixed $value
     * @return string
     */
    public static function formatFloat($value) {
        return number_format($value, 2, ',', '.');
    }

    /**
     * @param string $value
     * @return string
     */
    public static function formatDateTime($value) {
        return date('d/m/Y H:i:s', strtotime($value));
    }

    /**
     * @param string $value
     * @return string
     */
    public static function formatDate($value) {
        return date('d/m/Y', strtotime($value));
    }

    /**
     * @param string $value
     * @return string
     */
    public static function formatTime($value) {
        return date('H:i:s', strtotime($value));
    }
}