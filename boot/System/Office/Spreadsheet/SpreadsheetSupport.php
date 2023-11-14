<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Office\Spreadsheet;

use Boot\System\Office\OfficeSupport;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SpreadsheetSupport extends OfficeSupport
{
    protected $excel;
    protected $count_row;
    protected $count_column;
    protected $fillable;
    protected $data;
    protected $file;

    static $startLine = 1;

    public function get_parse_data($file)
    {
        $this->init_file($file);
        $this->field_name();
        return $this->parse_data();
    }

    public function get_excel_data($file)
    {
        $this->init_file($file);
        return $this->excel_to_array();
    }

    public function init_file($file)
    {
        if ($file instanceof UploadedFile) {
            $tmp_name = $file->getPathName();
        } else {
            if (!isset($file["tmp_name"])) throw new \Exception("Empty `tmp_name`!");
            $tmp_name = $file["tmp_name"];
        }
        if (!file_exists($tmp_name)) throw new \Exception("File `{$tmp_name}` not found!");
        $this->file = $file;
        $this->excel = IOFactory::load($tmp_name);
        $this->count_row =  $this->excel->setActiveSheetIndex(0)->getHighestRow(); //количество строк
        $countColumnAlp =  $this->excel->setActiveSheetIndex(0)->getHighestColumn(); //количество столбцов в буквенном виде
        $this->count_column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($countColumnAlp); //количество столбцов в числовом виде с 0
        return $this->excel;
    }

    public function get_field_name()
    {
        if (!$this->fillable) $this->field_name();
        return $this->fillable;
    }

    public function get_excel()
    {
        return $this->excel;
    }

    public function get_count_row()
    {
        return $this->count_row;
    }

    public function get_count_column()
    {
        return $this->count_column;
    }

    public function field_name()
    {
        $this->fillable = [];
        for ($i = 1; $i <= $this->count_column; $i++) {
            $arrayRes = [];
            $str = $this->excel->getActiveSheet()->getCellByColumnAndRow($i, 1)->getValue();
            preg_match('/[{]%.+%[}]/ui', $str, $arrayRes);
            $this->fillable[$i] = empty($arrayRes[0]) ? null : str_replace('%}', '', str_replace('{%', '', $arrayRes[0]));
        }
    }

    public function parse_data()
    {
        $this->data = [];
        $counter = 0;
        //проход по строкам
        for ($i = 2; $i <= $this->count_row; $i++) {
            //проход по столбцам
            $this->data[$counter] = [];
            for ($j = self::$startLine; $j <= $this->count_column; $j++) {
                $fj = $this->fillable[$j];
                if (empty($fj)) continue;
                $val = $this->excel->getActiveSheet()->getCellByColumnAndRow($j, $i)->getFormattedValue();
                $this->data[$counter][$fj] = $val;
            }
            $counter++;
        }
        return $this->data;
    }

    public function excel_to_array()
    {
        $this->data = [];
        $counter = 0;
        //проход по строкам
        for ($i = 1; $i <= $this->count_row; $i++) {
            //проход по столбцам
            $this->data[$counter] = [];
            for ($j = self::$startLine; $j <= $this->count_column; $j++) {
                $val = $this->excel->getActiveSheet()->getCellByColumnAndRow($j, $i)->getFormattedValue();
                $this->data[$counter][] = $val;
            }
            $counter++;
        }
        return $this->data;
    }

    static public function data_to_excel($request)
    {
        self::get_file(self::data_to_excel_file($request));
    }

    static public function data_to_excel_file($request)
    {
        //["head" => ["", ""], "data" => [[], [], []], ]
        //"filename" => ""
        if (isset($request["head"])) {
            $head = $request["head"];
        } else {
            throw new \Exception("Field `head` not found!");
        }
        if (isset($request["data"])) {
            $data = $request["data"];
        } else {
            throw new \Exception("Field `data` not found!");
        }
        $filename = isset($request["filename"]) ? $request["filename"] : time();

        $file = $filename . ".xlsx";

        $document = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $document->setActiveSheetIndex(0);
        $columnPosition = 1; // Начальная координата x
        $startLine = self::$startLine; // Начальная координата y

        foreach ($head as $item) {
            $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $item);
            $startLine++;
        }

        foreach ($data as $line) {
            $startLine = self::$startLine;
            $columnPosition++;
            foreach ($line as $item) {
                $sheet->setCellValueByColumnAndRow($startLine, $columnPosition, $item);
                $startLine++;
            }
        }

        $objWriter = IOFactory::createWriter($document, 'Xlsx');
        $objWriter->save($file);

        return $file;
    }
}
