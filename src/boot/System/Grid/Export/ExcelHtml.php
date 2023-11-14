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

class ExcelHtml extends Export
{
    protected $html;

    public function doExport()
    {
        $this->buildTable();
        $title = $this->title ?: $this->grid_id;
        $this->output($title . '.xls');
    }

    protected function buildTable()
    {
        $t = '<table border="1">';

        //--------------
        // Колонки
        //--------------

        $t .= '<tr>';
        foreach($this->cols as $k => $c)
        {
            if((($c['hidden'])&&!($c['excel_only'])) or $c['unset'] or $c['excel_hidden']) continue;

            $t .= '<th>' . $c['label'] . '</th>';
        }

        $t .= '</tr>';

        //--------------
        // Строки
        //--------------

        foreach($this->rows as $r)
        {
            $i = -1; //Индекс столбца

            $t .= '<tr>';

            foreach($this->cols as $k => $c)
            {
                if($c['unset']) continue;

                $i++;

                if(($c['hidden'])&&!($c['excel_only'])) continue;
                if($c['excel_hidden']) continue;

                $val = $r['cell'][$i];

                if(is_numeric($val)) $val = str_replace('.', ',', $val);

                $t .= '<td align="' . $c['align'] . '">' . $val . '</td>';
            }

            $t .= '</tr>';
        }

        if(isset($this->userdata['agg']) and array_intersect_key($this->cols, $this->userdata['agg']))
        {
            $t .= '<tr>';
            foreach($this->cols as $k => $c)
            {
                if(($c['hidden'])&&!($c['excel_only'])) continue;
                if($c['excel_hidden']) continue;
                $t .= '<td align="' . $c['align'] . '"><b>' . (isset($this->userdata['agg'][$k]) ? str_replace('.', ',', $this->userdata['agg'][$k]) : '') . '</b></td>';
            }
            $t .= '</tr>';
        }

        $t .= '</table>';

        $this->html = $t;
    }

    public function output($filename, $path = 'php://output')
    {
        //--------------
        // Заголовок ответа
        //--------------

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$filename;");

        //--------------
        // Контент
        //--------------

        $content = <<<EOF
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset={$this->getSetting('encoding')}" />
</head>
<body>
	{$this->html}
</body>
</html>
EOF;
        file_put_contents($path, $content);
    }
}