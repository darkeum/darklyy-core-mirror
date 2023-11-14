<?php

namespace Economic;

use Carbon\Carbon;
use Boot\System\Office\OfficeSupport;
use PhpOffice\PhpWord\Style\Language;

class ActPrintController extends ActViewController{
    protected $line_break = "<w:br/>";
    protected $size = "567.2";
    protected $space = ['spaceBefore' => 0, 'spaceAfter' => 0];
    protected $style = ['name' => 'Arial', 'size' => 8];
    protected $empty_symbol = "⠀";
    
    public function print(){
        try{
            $this->phpWord = new \PhpOffice\PhpWord\PhpWord();
            $this->phpWord->getSettings()->setThemeFontLang(new Language(Language::RU_RU));
            $this->section = $this->phpWord->addSection([
                'orientation' => 'landscape',
                "marginTop" => 1 * $this->size, 
                "marginLeft" => 1.27 * $this->size, 
                "marginRight" => 2.5 * $this->size, 
                "marginBottom" => 1 * $this->size,
            ]);
    
            $this->head();
            $this->empty_line();
            $this->table();
            $this->empty_line();
            $this->footer();
    
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($this->phpWord, 'Word2007');
            $file_name = "Акт сверки взаиморасчетов № {$this->numb} от ".Carbon::now()->locale('ru')->isoFormat("DD MMMM YYYY")." г.docx";
            $objWriter->save($file_name);
            OfficeSupport::get_file($file_name);
        } catch(\Exception $e){
            dd($e->getMessage());
        }
    }

    public function empty_line(){
        $this->section->addText("", $this->style, $this->space);
    }

    public function head(){
        $this->empty_line();
        $hl = $this->head_lines();
        $space = $this->space;
        $space["alignment"] = "center";
        $this->section->addText(
            $hl[0],
            ['name' => 'Arial', 'bold' => true, 'size' => 14], $space
        );
        $this->section->addText(
            $hl[1].$this->line_break.
            $hl[2].$this->line_break.
            $hl[3].(empty($hl[4]) ? $hl[4] : $this->line_break.$hl[4]),
            ['name' => 'Arial', 'size' => 10], $space
        );
        $space["alignment"] = "both";
        $this->empty_line();
        $this->section->addText(
            $this->head_text(),
            ['name' => 'Arial', 'size' => 10], $space
        );
    }

    public function table(){
        $this->height = 0.11 * $this->size;
        $this->cellMargin = 0.19 * $this->size;
        $this->table = $this->section->addTable(["borderSize" => 5,"cellMarginLeft" => $this->cellMargin, "cellMarginRight" => $this->cellMargin, "cellMarginTop" => 0, "cellMarginBottom" => 0]);
        $cell_width = 12.96 * $this->size;
        $this->cell_width_date = 1.48 * $this->size;
        $this->cell_width_doc = 5.56 * $this->size;
        $this->cell_width_dc = 2.96 * $this->size;
        $tr = [];
        $tr[0] = function($name) use ($cell_width){
            $cell = $this->table->addCell($cell_width);
            $cell->addText("По данным {$name}, руб.", $this->style, $this->space);
            $cell->getStyle()->setGridSpan(4);
        };
        $tr[1] = function(){
            $space = $this->space;
            $space["alignment"] = "center";
            $this->table->addCell($this->cell_width_date)->addText("Дата", $this->style, $space);
            $this->table->addCell($this->cell_width_doc)->addText("Документ", $this->style, $space);
            $this->table->addCell($this->cell_width_dc)->addText("Дебет", $this->style, $space);
            $this->table->addCell($this->cell_width_dc)->addText("Кредит", $this->style, $space);
        };
        
        $this->table->addRow($this->height);
        $tr[0]($this->org_data->name); $tr[0]($this->oid_data->name);

        $this->table->addRow($this->height);
        $tr[1](); $tr[1]();

        $this->table->addRow($this->height);
        if($this->initial_balance > 0){
            $this->tr2($this->initial_balance_text, $this->initial_balance, null); $this->tr2($this->initial_balance_text, null, $this->initial_balance);
        } else {
            $abs = abs($this->initial_balance);
            $this->tr2($this->initial_balance_text, null, $abs); $this->tr2($this->initial_balance_text, $abs, null);
        }

        $this->table_records();

        $this->table->addRow($this->height);
        $this->tr2("Обороты за период", $this->debit_total, $this->credit_total); 
        $this->tr2("Обороты за период", $this->fill_type_value($this->credit_total), $this->fill_type_value($this->debit_total));

        $this->table->addRow($this->height);
        if($this->final_balance > 0){
            $this->tr2($this->final_balance_text, $this->final_balance, null); $this->tr2($this->final_balance_text, null, $this->fill_type_value($this->final_balance));
        } else {
            $abs = abs($this->final_balance);
            $this->tr2($this->final_balance_text, null, $abs); $this->tr2($this->final_balance_text, $this->fill_type_value($abs), null);
        }
    }

    public function tr_clear($text, $debit, $credit, $style, $space){
        $debit = $debit == 0 ? $this->empty_symbol : static::price_format($debit);
        $credit = $credit == 0 ? $this->empty_symbol : static::price_format($credit);
        $space2 = $this->space;
        $space2["alignment"] = "right";
        $cell = $this->table->addCell(7.04 * $this->size);
        $cell->addText($text, $style, $space);
        $cell->getStyle()->setGridSpan(2);
        $this->table->addCell($this->cell_width_dc)->addText($debit, $style, $space2);
        $this->table->addCell($this->cell_width_dc)->addText($credit, $style, $space2);
    }

    public function tr2($text, $debit, $credit){
        $style = $this->style;
        $style["bold"] = true;
        $this->tr_clear($text, $debit, $credit, $style, $this->space);
    }

    public function tr22($text, $debit, $credit){
        $style = $this->style;
        $style["bold"] = false;
        $space = $this->space;
        $space["alignment"] = "right";
        $this->tr_clear($text, $debit, $credit, $style, $space);
    }

    public function tr3($item, $type){
        $space = $this->space;
        $space["alignment"] = "right";
        $space2 = $this->space;
        $space2["alignment"] = "center";
        $date = date("d.m.y", strtotime($item->date));
        $document = $item->$type->document;
        $debit_view = empty($item->debit_view) ? $this->empty_symbol : $item->debit_view;
        $credit_view = empty($item->credit_view) ? $this->empty_symbol : $item->credit_view;
        if($type == "oid_data"){
            $date = $this->fill_type_value($date);
            $document = $this->fill_type_value($document);
            $debit_view = $this->fill_type_value($debit_view);
            $credit_view = $this->fill_type_value($credit_view);
        }
        $this->table->addCell($this->cell_width_date)->addText($date, $this->style, $space2);
        $this->table->addCell($this->cell_width_doc)->addText($document, $this->style, $this->space);
        $this->table->addCell($this->cell_width_dc)->addText($debit_view, $this->style, $space);
        $this->table->addCell($this->cell_width_dc)->addText($credit_view, $this->style, $space);
    }

    public function table_records_items($item, $debit, $credit){
        $this->table->addRow($this->height);
        $this->tr3($item, "org_data"); 
        $this->tr3($item, "oid_data");
    }

    public function tr_contract($item, $initial_contract){
        $this->table->addRow($this->height);
        $cell = $this->table->addCell(25.93 * $this->size);
        $cell->addText($item->contract_name, $this->style, $this->space);
        $cell->getStyle()->setGridSpan(8);
        $this->table->addRow($this->height);
        if($initial_contract > 0){
            $this->tr22($this->initial_balance_text, $initial_contract, null);
            $this->tr22($this->initial_balance_text, null, $initial_contract);
        } else {
            $abs = abs($initial_contract);
            $this->tr22($this->initial_balance_text, null, $abs);
            $this->tr22($this->initial_balance_text, $abs, null);
        }
    }

    public function tre($initial_contract, $debit_contract, $credit_contract){
        $this->table->addRow($this->height);
        $this->tr22($this->contract_turnover, $debit_contract, $credit_contract);
        $this->tr22($this->contract_turnover, $this->fill_type_value($credit_contract), $this->fill_type_value($debit_contract));
        $this->table->addRow($this->height);
        $final_balance = $this->calc_final_balance($initial_contract, $debit_contract, $credit_contract);
        if($final_balance > 0){
            $this->tr22($this->final_balance_text, $final_balance, null);
            $this->tr22($this->final_balance_text, $this->fill_type_value(null), $this->fill_type_value($final_balance));
        } else {
            $abs = abs($final_balance);
            $this->tr22($this->final_balance_text, null, $abs);
            $this->tr22($this->final_balance_text, $this->fill_type_value($abs), $this->fill_type_value(null));
        }
    }

    public function footer(){
        $table = $this->section->addTable(["cellMarginLeft" => $this->cellMargin, "cellMarginRight" => $this->cellMargin, "cellMarginTop" => 0, "cellMarginBottom" => 0]);

        $text = $this->footer_text();
        $text2 = $this->fill_type_value($text);

        $add_row = function() use ($table){
            $table->addRow();
            $cell1 = $table->addCell(12.5 * $this->size);
            $table->addCell(0.42 * $this->size)->addText("", $this->style, $this->space);
            $cell2 = $table->addCell(12.99 * $this->size);
            return [
                $cell1, $cell2,
            ];
        };
        $add_empty_row = function() use ($add_row){
            $cell = $add_row();
            $cell[0]->addText("", $this->style, $this->space);
            $cell[1]->addText("", $this->style, $this->space);
        };

        $cell = $add_row();
        $cell[0]->addText("По данным {$this->org_data->name}", $this->style, $this->space);
        $cell[1]->addText("По данным {$this->oid_data->name}", $this->style, $this->space);

        $cell = $add_row();
        $style2 = $this->style;
        $style2["bold"] = true;
        $cell[0]->addText("{$text}", $style2, $this->space);
        $cell[1]->addText("{$text2}", $style2, $this->space);

        $add_empty_row();
        $cell = $add_row();
        $cell[0]->addText("От {$this->org_data->name}", $this->style, $this->space);
        $cell[1]->addText("От {$this->oid_data->name}", $this->style, $this->space);

        $add_empty_row();
        $cell = $add_row();
        $cell[0]->addText("Директор", $this->style, $this->space);
        $cell[1]->addText("{$this->oid_post}", $this->style, $this->space);

        $add_empty_row();
        $cell = $add_row();
        $cell[0]->addText("{$this->line} ({$this->org_director_short})", $this->style, $this->space);
        $cell[1]->addText("{$this->line} ({$this->oid_director_short})", $this->style, $this->space);

        $add_empty_row();
        $cell = $add_row();
        $cell[0]->addText($this->mp, $this->style, $this->space);
        $cell[1]->addText($this->mp, $this->style, $this->space);
    }
}