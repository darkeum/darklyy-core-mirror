<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Grid\Export;

class ExcelXml extends Export
{
    protected $encoding;

    public function doExport()
    {
        $this->encoding = $this->getSetting('encoding');

        $this->xml = simplexml_load_string($this->getBaseXml());
    }

    public function setData(array $cols, array $rows)
    {
        $col_count = 0;
        $row_count = 0;

        //-------------
        // Установка времени
        //-------------

        $this->xml->DocumentProperties->Created = date('Y-m-d\TH:i:s\Z');

        //-------------
        // Установка таблицы
        //-------------

        $tbl = $this->xml->Worksheet->Table;

        //-------------
        // Колонки
        //-------------

        foreach($cols as $k => $c)
        {
            if($c['hidden'] or $c['unset'])
            {
                continue;
            }

            $column = $tbl->addChild('Column');
            $column['ss:AutoFitWidth'] = 1;
            $column['ss:Width'] = $c['width'] * 2;

            $col_count++;
        }

        //-------------
        // Строка заголовков
        //-------------

        $row = $tbl->addChild('Row');
        $row_count++;

        foreach($cols as $k => $c)
        {
            if($c['hidden'] or $c['unset'])
            {
                continue;
            }

            $cell = $row->addChild('Cell');
            $cell['ss:StyleID'] = 's22';

            $data = $cell->addChild('Data', $this->encode($c['name']));
            $data['ss:Type'] = 'String';
        }

        //-------------
        // Строки
        //-------------

        foreach($rows as &$r)
        {
            $i = -1; //Индекс строки
            $row = $tbl->addChild('Row');

            foreach($cols as $k => $c)
            {
                if($c['unset']) continue;

                $i++;

                if($c['hidden']) continue;

                $cell = $row->addChild('Cell');
                $cell['ss:StyleID'] = 's23';

                $data = $cell->addChild('Data', $this->encode($r['cell'][$i]));
                $data['ss:Type'] = is_numeric($r['cell'][$i]) ? 'Number' : 'String';
            }

            $row_count++;
        }

        unset($r);

        //-------------
        // Обновление таблицы
        //-------------

        $tbl['ss:ExpandedColumnCount'] = $col_count;
        $tbl['ss:ExpandedRowCount'] = $row_count;
    }

    public function output($filename = 'excel.xml', $path = '')
    {
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-type: application/vnd.ms-excel");

        header("Content-Disposition: attachment; filename=$filename;");
        header("Content-Transfer-Encoding: binary");

        $path = $path ? $path : 'php://output';
        $this->xml->asXml($path);
    }

    protected function encode($val)
    {
        if(is_numeric($val))
        {
            return $val;
        }

        $val = htmlspecialchars($val);
        $val = str_replace("\n", '&#10;', $val);

        if($this->encoding != 'utf-8')
        {
            $val = iconv($this->encoding, 'utf-8', $val);
        }

        return $val;
    }

    protected function getBaseXml()
    {
        return
            <<<EOF
<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>jqGridExportExcel</Author>
  <LastAuthor>jqGridExportExcel</LastAuthor>
  <Created>2011-05-18T10:45:44Z</Created>
  <Version>11.9999</Version>
 </DocumentProperties>
 <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
 </ExcelWorkbook>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Bottom"/>
   <Borders/>
   <Font ss:FontName="Arial Cyr" x:CharSet="204"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="s22">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial Cyr" x:CharSet="204" ss:Bold="1"/>
  </Style>
  <Style ss:ID="s23">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>
 </Styles>
 <Worksheet ss:Name="Лист1">
  <Table x:FullColumns="1" x:FullRows="1"></Table>
 </Worksheet>
 <x:ExcelWorkbook>  </x:ExcelWorkbook>
</Workbook>
EOF;
    }
}