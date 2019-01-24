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

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\DataSource\DataSourceInterface;
use Eulogix\Cool\Lib\PHPExcel\CoolExcelDate;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Worksheet;
use PHPExcel_Writer_Excel2007;
use Eulogix\Cool\Lib\Lister\Column;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ExcelRenderer extends BaseRenderer {


    /*
     * @var PHPExcel
     */
    private $phpExcelObject;

    public function getPHPExcelObject(){
        return $this->phpExcelObject;
    }

    public function setPHPExcelObject(PHPExcel $newPHPExcelObject){
        $this->phpExcelObject = $newPHPExcelObject;
    }

    public function __construct(DataSourceInterface $ds)
    {
        parent::__construct($ds);

        $this->phpExcelObject = new PHPExcel();
    }

    /**
     * @inheritdoc
     */
    public function renderData(array $rows, $raw, array $listerColumnsDefinitions=null)
    {
        $tracker = $this->getProgressTracker();

        $rows = $this->datesPHPToExcel($rows);

        $visibleColumnNames = array_keys($listerColumnsDefinitions);
        $filteredRows = $this->filterData($rows, $visibleColumnNames);

        $visibleColumnNamesDecoded = array_map(function($c){
            /**
             * @var Column $c
             */
            return $c->getLabel();
        }, $listerColumnsDefinitions);

        if(!$raw) {
            $this->fillSheet('Data', $listerColumnsDefinitions, $visibleColumnNamesDecoded, $this->getDecodedRows($filteredRows));

            $this->getPHPExcelObject()->createSheet();
            $this->getPHPExcelObject()->setActiveSheetIndex( $this->getPHPExcelObject()->getActiveSheetIndex()+1 );
        }
        $tracker->logProgress(25);

        $visibleColumnNamesRaw = array_map(function($c){
            /**
             * @var Column $c
             */
            return $c->getName();
        }, $listerColumnsDefinitions);

        $this->fillSheet('Raw Data',$listerColumnsDefinitions,$visibleColumnNamesRaw,$this->getRawRows($filteredRows));

        $tracker->logProgress(50);

        if(!$raw) {
            $this->getPHPExcelObject()->createSheet();
            $this->getPHPExcelObject()->setActiveSheetIndex( $this->getPHPExcelObject()->getActiveSheetIndex()+1 );

            $fullData = $this->getDecodedRows($rows);

            $this->fillSheet('Data - expanded',array_keys($fullData[0]),array_keys($fullData[0]),$fullData );

        }

        $tracker->logProgress(75);

        $this->getPHPExcelObject()->createSheet();
        $this->getPHPExcelObject()->setActiveSheetIndex( $this->getPHPExcelObject()->getActiveSheetIndex()+1 );

        $fullData = $this->getRawRows($rows);

        $this->fillSheet('Raw Data - expanded',array_keys($fullData[0]),array_keys($fullData[0]),$fullData);

        $this->getPHPExcelObject()->setActiveSheetIndex(0);

        $writer = new PHPExcel_Writer_Excel2007( $this->getPHPExcelObject() );

        $t = tempnam(Cool::getInstance()->getFactory()->getSettingsManager()->getTempFolder(),"XLSX");
        $writer->save($t);
        $ret = file_get_contents($t);
        @unlink($t);

        $tracker->logProgress(100);

        return $ret;
    }

    /**
     * @param string[] $headerLabels
     * @param PHPExcel_Worksheet $sheet
     * @param int $cellStart
     */
    private function renderHeaders(array $headerLabels, PHPExcel_Worksheet $sheet, $cellStart) {
        // TODO: xlsx formatting using columns settings like xlsx width, data type...
        $headerFormat = $this->getFormats()['header'];

        for($i=0; $i<count($headerLabels); $i++) {
            $columnCode = PHPExcel_Cell::stringFromColumnIndex($cellStart + $i);
            $sheet->getColumnDimension( $columnCode )->setWidth(20);
            $sheet->getStyle( $columnCode.'1' )->applyFromArray($headerFormat);
        }

        $sheet->fromArray($headerLabels, null, PHPExcel_Cell::stringFromColumnIndex($cellStart).'1');

        $lastColumnIndex = count($headerLabels)-1;
        $columnInterval = "A1:".PHPExcel_Cell::stringFromColumnIndex($lastColumnIndex)."1";
        $this->getPHPExcelObject()->getActiveSheet()->setAutoFilter($columnInterval);
    }

    /**
     * @return array
     */
    private function getFormats() {
        return  array(
            'header'=>       array(
                'font' => array(
                    'name' => 'Arial',
                    'size' => 9,
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000')
                    ),
                ),
                'numberformat'=> array(
                    'code' => 'dd-[$-410]mmm',
                ),
            )
        );
    }

    /**
     * @param array $data
     * @return array
     * Process an the array $data looking for strings with date format and returns one
     *     changing the strings that have date format by an excel date integer value
     */
    protected function datesPHPToExcel(array $data) {
        $ret = $data;
        foreach($ret as &$row) {
            $rowKeys = array_keys($row);
            foreach ($rowKeys as $key){
                if ($this->isDateColumn($key)){
                    $date = strtotime(@$row[$key]);
                    @$row[$key]=CoolExcelDate::PHPToExcel($date);
                    @$row[DataSourceInterface::DECODIFICATIONS_IDENTIFIER][$key]=CoolExcelDate::PHPToExcel($date);
                }
            }
        }
        return $ret;
    }

    /**
     * @param array $columnsDefinitions
     * @param int $rowsNumber
     * Locate the columns that should contain dates, and assign then the date format
     */
    private function assignSpecialColumnTypes(array $columnsDefinitions, int $rowsNumber): void
    {
        foreach ($columnsDefinitions as $key => $column) {
            $needle = is_numeric($key)?$column:$key;

            if ($this->isDateColumn($needle)){
                $columnIndex = is_numeric($key)?$key:array_search($needle, array_keys($columnsDefinitions));
                $columnLetter = PHPExcel_Cell::stringFromColumnIndex($columnIndex);
                $cellInterval = $columnLetter . "2:" . $columnLetter . ($rowsNumber + 1);
                $phpExcelDateFormatString = \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY;
                $this->getPHPExcelObject()->getActiveSheet()->getStyle($cellInterval)->getNumberFormat()->setFormatCode($phpExcelDateFormatString);
            }

            if ($this->isDecimalColumn($needle)){
                $columnIndex = is_numeric($key)?$key:array_search($needle, array_keys($columnsDefinitions));
                $columnLetter = PHPExcel_Cell::stringFromColumnIndex($columnIndex);
                $cellInterval = $columnLetter . "2:" . $columnLetter . ($rowsNumber + 1);
                $this->getPHPExcelObject()->getActiveSheet()->getStyle($cellInterval)->getNumberFormat()->setFormatCode('#,##0.00');
            }
        }
    }

    /**
     * @param $sheetTitle
     * @param array $columnsDefinitions
     * @param $headers
     * @param $rows
     */
    protected function fillSheet($sheetTitle, array $columnsDefinitions, $headers, $rows): void
    {
        $this->getPHPExcelObject()->getActiveSheet()->setTitle($sheetTitle);
        $this->renderHeaders($headers, $this->getPHPExcelObject()->getActiveSheet(), 0);
        $this->getPHPExcelObject()->getActiveSheet()->fromArray($rows, null, 'A2');
        $this->assignSpecialColumnTypes($columnsDefinitions,count($rows));
    }


    private function isDateColumn($fieldName){
        $ret = FALSE;

        if ($field = $this->getDataSource()->getField($fieldName) ){
            if ($columnType = $field->getType()){
                if ($columnType == 'DATE' || $columnType == 'TIMESTAMP'){
                    $ret = TRUE;
                }
            }

        }
        return $ret;
    }

    private function isDecimalColumn($fieldName){
        $ret = FALSE;

        if ($field = $this->getDataSource()->getField($fieldName) ){
            if ($columnType = $field->getType()){
                if ($columnType == 'DECIMAL'){
                    $ret = TRUE;
                }
            }

        }
        return $ret;
    }
}
