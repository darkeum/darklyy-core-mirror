<?php

namespace Catalog\Stock\Provider;

use \Catalog\Stock\Parser;
use Boot\System\Office\Spreadsheet\SpreadsheetSupport;

class IMP extends Parser{

    use \Catalog\Stock\Traits\EmailSRV;

    protected $from = "1c-service@imp-pumps.info";

    public function getManufacture(){
        return 271;
    }

    public function parse_(){
        $email = $this->emailDownload();
        
        // $date = "2022-06-16";
        $date = $email->date;

        // $files_patch = [static::$tempFiles."Остатки и доступность товаров -для рассылки (XLSX) (1).xlsx"];
        $files_patch = $email->files_patch;

        if(count($files_patch) > 0){
            foreach($files_patch as $file_patch){
                $excel = new SpreadsheetSupport;
                $excel->init_file(["tmp_name" => $file_patch]);
                $data = $excel->excel_to_array();
                $len = count($data) - 1;
                for($i = 11; $i < $len; $i++){
                    $item = $data[$i];
                    $sku =   static::prepareValue($item[0]);
                    $name =  static::prepareValue($item[3]);
                    $count = (double)static::prepareValue($item[12]);
                    $catalog = $this->findCatalog($name, $sku);
                    static::dumpData($sku, $name, $count, $catalog);
                    foreach($catalog as $item){
                        $this->updateOrCreate($item->id, $count, $date);
                    }
                }
                unlink($file_patch);
            }
            $this->catalogManufactureStockParserUpdate(["last_date_file" => $date]);
        }
        
    }
}