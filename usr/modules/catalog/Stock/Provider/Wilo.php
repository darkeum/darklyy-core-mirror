<?php

namespace Catalog\Stock\Provider;

use \Catalog\Stock\Parser;

class Wilo extends Parser
{
    use \Catalog\Stock\Traits\EmailSRV;

    protected $from = "wru.sales@wilo.com";

    public function getManufacture()
    {
        return 20;
    }

    public function parse_()
    {
        $email = $this->emailDownload();
        $date = $email->date;

        // $files_patch = [static::$tempFiles."Job RUSTOCKSALL, Step 1.htm"];
        $files_patch = $email->files_patch;
        
        if(count($files_patch) > 0){
            foreach($files_patch as $file_patch){
                if(file_exists($file_patch)){
                    $dom = new \PHPHtmlParser\Dom;
                    $dom->loadStr(file_get_contents($file_patch));
                    $tables = $dom->find('table');
                    foreach($tables as $table){
                        $tbody = $table->find("tbody");
                        $trs = $tbody[0]->find("tr");
                        if(isset($trs[0]) && count($trs[0]->find("td")) == 5){
                            for($i=1; $i<count($trs); $i++){
                                $td = $trs[ $i ]->find("td");
                                
                                $sku =      static::prepareValue($td[1]->find("nobr")[0]->text);
                                $name =     static::prepareValue($td[2]->find("nobr")[0]->text);
                                $count =    (double)static::prepareValue($td[3]->find("nobr")[0]->text);
        
                                $catalog = $this->findCatalog($name, $sku);
                                static::dumpData($sku, $name, $count, $catalog);
                                foreach($catalog as $item){
                                    $this->updateOrCreate($item->id, $count, $date);
                                }
                            }
                        }
                    }
        
                    unlink($file_patch);
                }
            }
            $this->catalogManufactureStockParserUpdate(["last_date_file" => $date]);
        }
    }
}
