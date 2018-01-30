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

    /**
     * @inheritdoc
     */
    public function renderData(array $rows, $raw, array $listerColumnsDefinitions=null)
    {
        $tracker = $this->getProgressTracker();

        $visibleColumnNames = array_keys($listerColumnsDefinitions);
        $filteredRows = $this->filterData($rows, $visibleColumnNames);

        $visibleColumnNamesDecoded = array_map(function($c){
            /**
             * @var Column $c
             */
            return $c->getLabel();
        }, $listerColumnsDefinitions);

        $phpExcelObject = new PHPExcel();

        if(!$raw) {
            $phpExcelObject->getActiveSheet()->setTitle('Data');
            $this->renderHeaders($visibleColumnNamesDecoded, $phpExcelObject->getActiveSheet(), 0);
            $phpExcelObject->getActiveSheet()->fromArray($this->getDecodedRows($filteredRows), null, 'A2');
            $phpExcelObject->createSheet();
            $phpExcelObject->setActiveSheetIndex( $phpExcelObject->getActiveSheetIndex()+1 );
        }
        $tracker->logProgress(25);

        $phpExcelObject->getActiveSheet()->setTitle('Raw Data');
        $this->renderHeaders($visibleColumnNames, $phpExcelObject->getActiveSheet(), 0);
        $phpExcelObject->getActiveSheet()->fromArray($this->getRawRows($filteredRows), null, 'A2');

        $tracker->logProgress(50);

        if(!$raw) {
            $phpExcelObject->createSheet();
            $phpExcelObject->setActiveSheetIndex( $phpExcelObject->getActiveSheetIndex()+1 );

            $fullData = $this->getDecodedRows($rows);
            $phpExcelObject->getActiveSheet()->setTitle('Data - expanded');
            $this->renderHeaders(array_keys($fullData[0]), $phpExcelObject->getActiveSheet(), 0);
            $phpExcelObject->getActiveSheet()->fromArray($fullData, null, 'A2');
        }

        $tracker->logProgress(75);

        $phpExcelObject->createSheet();
        $phpExcelObject->setActiveSheetIndex( $phpExcelObject->getActiveSheetIndex()+1 );

        $fullData = $this->getRawRows($rows);
        $phpExcelObject->getActiveSheet()->setTitle('Raw Data - expanded');
        $this->renderHeaders(array_keys($fullData[0]), $phpExcelObject->getActiveSheet(), 0);
        $phpExcelObject->getActiveSheet()->fromArray($fullData, null, 'A2');

        $phpExcelObject->setActiveSheetIndex(0);

        $writer = new PHPExcel_Writer_Excel2007( $phpExcelObject );

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

}