<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2024 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Office\Spreadsheet;

use Exception;
use Boot\System\Office\OfficeSupport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SpreadsheetSupport extends OfficeSupport
{
    protected Spreadsheet $excel;
    protected int $count_row;
    protected int $count_column;
    protected array $fillable;
    protected array $data;
    protected UploadedFile|array $file;

    static int $startLine = 1;

    /**
     * Получение данных по ключевым полям из excel
     *
     * @param   UploadedFile|array $file Источник файла
     * @return array
     */
    public function get_parse_data($file): array
    {
        $this->init_file($file);
        $this->field_name();
        return $this->parse_data();
    }

    /**
     * Получение всех данных из excel
     *
     * @param   UploadedFile|array $file Источник файла
     * @return array
     */
    public function get_excel_data($file): array
    {
        $this->init_file($file);
        return $this->excel_to_array();
    }

    /**
     * Инициализация excel файла
     *
     * @param  UploadedFile|array $file Источник файла
     * @return Spreadsheet
     */
    public function init_file(UploadedFile|array $file): Spreadsheet
    {
        if ($file instanceof UploadedFile) {
            $tmp_name = $file->getPathName();
        } else {
            if (!isset($file["tmp_name"])) throw new Exception("Отсутствует `tmp_name` в источнике файла!");
            $tmp_name = $file["tmp_name"];
        }
        if (!file_exists($tmp_name)) throw new Exception("Файл `{$tmp_name}` не найден!");
        $this->file = $file;
        $this->excel = IOFactory::load($tmp_name);
        //Количество строк
        $this->count_row =  $this->excel->setActiveSheetIndex(0)->getHighestRow();
        //Количество столбцов в буквенном виде
        $countColumnAlp =  $this->excel->setActiveSheetIndex(0)->getHighestColumn();
        //Количество столбцов в числовом виде с 0
        $this->count_column = Coordinate::columnIndexFromString($countColumnAlp);
        return $this->excel;
    }

    /**
     * Получение списка полей из файлы excel
     *
     * @return array
     */
    public function get_field_name(): array
    {
        if (!$this->fillable) $this->field_name();
        return $this->fillable;
    }

    /**
     * Получение экземпляра Spreadsheet
     *
     * @return Spreadsheet
     */
    public function get_excel(): Spreadsheet
    {
        return $this->excel;
    }

    /**
     * Получение количества строк
     *
     * @return int
     */
    public function get_count_row(): int
    {
        return $this->count_row;
    }

    /**
     * Получение количества столбцов
     *
     * @return int
     */
    public function get_count_column(): int
    {
        return $this->count_column;
    }

    /**
     * Взятие списка полей из файлы excel по шаблону {%field_key%}, берется из первой строки
     *
     * @return array
     */
    public function field_name(): array
    {
        $this->fillable = [];
        for ($i = 1; $i <= $this->count_column; $i++) {
            $arrayRes = [];
            $str = $this->excel->getActiveSheet()->getCell([$i, 1])->getValue();
            preg_match('/[{]%.+%[}]/ui', $str, $arrayRes);
            $this->fillable[$i] = empty($arrayRes[0]) ? null : str_replace('%}', '', str_replace('{%', '', $arrayRes[0]));
        }
        return $this->fillable;
    }

    /**
     * Извлечение необходимых полей по $this->fillable из файла excel в массив
     *
     * @return array
     */
    public function parse_data(): array
    {
        return $this->excel_to_array(true);
    }

    /**
     * Извлечение данных из excel в массив
     *
     * @param  bool $fillable_only извлекать только теп поля которые есть $this->fillable
     * @return array
     */
    public function excel_to_array($fillable_only = false): array
    {
        $this->data = [];
        $counter = 0;
        for ($i = $fillable_only ? 2 : 1; $i <= $this->count_row; $i++) {
            $this->data[$counter] = [];
            for ($j = self::$startLine; $j <= $this->count_column; $j++) {
                $val = $this->excel->getActiveSheet()->getCell([$j, $i])->getFormattedValue();
                if ($fillable_only) {
                    $fj = $this->fillable[$j];
                    if (empty($fj)) continue;
                    $this->data[$counter][$fj] = $val;
                } else {
                    $this->data[$counter][] = $val;
                }
            }
            $counter++;
        }
        return $this->data;
    }

    /**
     * Создание excel по указанным данных и вывод их
     *
     * @param  array $request
     * @return void
     */
    static public function data_to_excel(array $request)
    {
        self::get_file(self::data_to_excel_file($request));
    }

    /**
     * Создание excel по указанным данных
     *
     * @param  array $request
     * @return string
     */
    static public function data_to_excel_file(array $request): string
    {
        $filename = isset($request["filename"]) ? $request["filename"] : uniqid();

        $file = $filename . ".xlsx";
        $document = new Spreadsheet();
        $sheet = $document->setActiveSheetIndex(0);
        $pages = [];
        if (!isset($request["pages"])) {
            $pages[] = $request;
        } else {
            $pages = $request["pages"];
        }
        foreach ($pages as $page) {

            if (isset($page["head"])) {
                $head = $page["head"];
            } else {
                throw new Exception("Отсутствует поле `head` необходимое для создания Excel!");
            }

            if (isset($page["data"])) {
                $data = $page["data"];
            } else {
                throw new Exception("Отсутствует поле `data` необходимое для создания Excel!");
            }

            if (isset($page['style'])) {
                $stylePage = $page['style'];
            }

            if (isset($page['charts'])) {
                $charts = $page['charts'];
            }

            if (isset($request["pages"])) {
                $sheet = $document->createSheet();
            }

            if (isset($stylePage['title'])) $sheet->setTitle($stylePage['title']);
            if (isset($stylePage['fix-header'])) $sheet->freezePane($stylePage['fix-header']);

            //Начальная координата x
            $columnPosition = 1;
            //Начальная координата y
            $startLine = self::$startLine;

            foreach ($head as $item) {
                $sheet->setCellValue([$startLine, $columnPosition], $item['value']);

                if (isset($item['style']['code'])) {
                    $sheet->getStyle([$startLine, $columnPosition])->getNumberFormat()->setFormatCode($item['style']['code']);
                }
                if (isset($item['style']['auto-size'])) {
                    if ($item['style']['auto-size']) {
                        $sheet->getColumnDimensionByColumn($startLine)->setAutoSize($item['style']['auto-size']);
                    } else {
                        $sheet->getColumnDimensionByColumn($startLine)->setAutoSize($item['style']['auto-size']);
                        $sheet->getColumnDimensionByColumn($startLine)->setWidth(50);
                    }
                }
                $sheet->getStyle([$startLine, $columnPosition])->applyFromArray($item['style']);
                $startLine++;
            }

            foreach ($data as $line) {
                $startLine = self::$startLine;
                $columnPosition++;
                foreach ($line as $item) {
                    $sheet->setCellValue([$startLine, $columnPosition], $item['value']);
                    $sheet->getStyle([$startLine, $columnPosition])->applyFromArray($item['style']);
                    if (isset($item['style']['code'])) {
                        $sheet->getStyle([$startLine, $columnPosition])->getNumberFormat()->setFormatCode($item['style']['code']);
                    }
                    if (isset($item['style']['auto-size'])) $sheet->getStyle([$startLine, $columnPosition])->getAlignment()->setWrapText(true);
                    $startLine++;
                }
            }

            if (isset($charts)) {
                foreach ($charts as $chart) {
                    $sheet->addChart($chart);
                }
                $charts = null;
            }
        }

        $objWriter = IOFactory::createWriter($document, 'Xlsx');
        $objWriter->setIncludeCharts(true);
        $objWriter->save($file);
        return $file;
    }
}
