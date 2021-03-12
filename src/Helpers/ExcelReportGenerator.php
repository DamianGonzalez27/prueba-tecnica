<?php

namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelReportGenerator
{
    private $activeSheet;
    private $config;

    public function _generate($data, $config = []){
        $row = 2;
        $column = 1;
        $spreadsheet = new Spreadsheet();
        $writer = new Xlsx($spreadsheet);
        $spreadsheet->setActiveSheetIndex(0);
        $this->activeSheet = $spreadsheet->getActiveSheet();
        $this->config = $config;
        $this->transStrings = isset($this->config['transStrings'])?$this->config['transStrings']:[];

        $this->drawValue($data, 2, 1);
        
        return $writer;
    }

    private function drawRow($rowData, $row, $column){
        $colIt = $column;
        $tallestRowSize = 0;
        foreach($rowData as $value){
            $drawnSize = $this->drawValue($value, $row, $colIt);
            $colIt += $drawnSize['width'];
            if($tallestRowSize<$drawnSize['height']){
                $tallestRowSize = $drawnSize['height'];
            }
        }
        return [
            'width'=>$colIt-$column,
            'height'=>$tallestRowSize,
        ];
    }

    private function isAssocArray($value){
        if(!is_array($value)){
            return false;
        }
        if(array()===$value){
            return false;
        }
        return array_keys($value) !== range(0, count($value)-1);
    }

    private function drawCol($cols, $row, $column){
        $rowIt = $row;
        $widestDrawn = 0;
        foreach($cols as $col){
            $drawnRect = $this->drawValue($col, $rowIt, $column);
            $rowIt += $drawnRect['height'];
            if($widestDrawn<$drawnRect['width']){
                $widestDrawn = $drawnRect['width'];
            }
        }
        return [
            'width'=>$widestDrawn,
            'height'=>$rowIt-$row
        ];
    }

    private function drawValue($value, $row, $column){
        if($this->isAssocArray($value)){
            return $this->drawRow($value, $row, $column);
        }
        elseif(is_array($value)){
            return $this->drawCol($value, $row, $column);
        }
        else{
            $cell = $this->activeSheet->getCellByColumnAndRow($column, $row);
            $this->formatCell($cell, $value);
            return ['width'=>1, 'height'=>1];
        }
    }

    public function generate($data, $config = [])
    {

        if (!isset($config['headers'])) {
            foreach ($data[0] as $path => $value) {
                $headers[$path] = $path;
            }
        }else{
            $headers = $config['headers'];
        }

        $transStrings = isset($config['transStrings'])?$config['transStrings']:[];

        $spreadsheet = new Spreadsheet();
        $writer = new Xlsx($spreadsheet);
        $spreadsheet->setActiveSheetIndex(0);
        $activeSheet = $spreadsheet->getActiveSheet();

        $this->renderHeaders($headers, 1, 1, $activeSheet);

        $row = 2;
        foreach ($data as $rowData) {
            $column = 1;
            foreach ($headers as $path=>$title) {
                $cell = $activeSheet->getCellByColumnAndRow($column, $row);
                if(!isset($rowData[$path])) {
                    $this->formatCell($cell, '', $transStrings);
                }
                else {
                    $this->formatCell($cell, $rowData[$path], $transStrings);
                }
                $column++;
            }
            $row++;
        }

        $this->resizeColumns($activeSheet);
        return $writer;
    }

    private function renderHeaders($headers, $headerColumn, $headersRow, $activeSheet)
    {
        /** @var  Worksheet $activeSheet */
        foreach ($headers as $path=>$title) {
            $blueColor = new Color("FF44ABE4");
            $whiteColor = new Color(Color::COLOR_WHITE);
            $cell = $activeSheet->getCellByColumnAndRow($headerColumn, $headersRow);
            $cell->setValue($title);
            $cell->getStyle()->getFont()->setBold(true);
            $cell->getStyle()->getFont()->setColor($whiteColor);
            $cell->getStyle()
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor($blueColor)
                ->setEndColor($blueColor);
            $headerColumn++;
        }
    }

    private function _formatCell($cell, $content){
        if(isset($this->transStrings[$content])){
            $content = $transStrings[$content];
        }
        $cell->setValue($content);
    }

    private function formatCell($cell, $content, $transStrings)
    {
        if(isset($transStrings[$content])){
            $content = $transStrings[$content];
        }

        $cell->setValue($content);
    }

    private function resizeColumns($activeSheet)
    {
        foreach (range('A', 'Z') as $columnID) {
            $activeSheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }

}
