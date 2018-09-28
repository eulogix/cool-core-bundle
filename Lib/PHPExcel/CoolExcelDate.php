<?php

namespace Eulogix\Cool\Lib\PHPExcel;


class CoolExcelDate extends \PHPExcel_Shared_Date
{

    /**
     *	Convert a date from PHP to Excel
     *
     *	@param	mixed		$dateValue			PHP serialized date/time or date object
     *	@param	boolean		$adjustToTimezone	Flag indicating whether $dateValue should be treated as
     *													a UST timestamp, or adjusted to UST
     *	@param	string	 	$timezone			The timezone for finding the adjustment from UST
     *	@return	mixed		Excel date/time value
     *							or boolean FALSE on failure
     */
    public static function PHPToExcel($dateValue = 0, $adjustToTimezone = FALSE, $timezone = NULL) {
        /*
         * this function was not using the variables of
         *      $adjustToTimezone and
         *      $timezones
         */
        $adjustToTimezone = $adjustToTimezone?$adjustToTimezone:'UTC';

        if(!in_array($adjustToTimezone, timezone_identifiers_list())){
          return FALSE;
        }

        $saveTimeZone = date_default_timezone_get();
        date_default_timezone_set('Europe/Madrid');
        $retValue = FALSE;
        if ((is_object($dateValue)) && ($dateValue instanceof DateTime)) {
            $retValue = self::FormattedPHPToExcel( $dateValue->format('Y'), $dateValue->format('m'), $dateValue->format('d'),
                $dateValue->format('H'), $dateValue->format('i'), $dateValue->format('s')
            );
        } elseif (is_numeric($dateValue)) {
            $retValue = self::FormattedPHPToExcel( date('Y',$dateValue), date('m',$dateValue), date('d',$dateValue),
                date('H',$dateValue), date('i',$dateValue), date('s',$dateValue)
            );
        }
        date_default_timezone_set($saveTimeZone);

        return $retValue;
    }	//	function PHPToExcel()

}