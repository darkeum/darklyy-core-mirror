<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
* @description Job Public Controller
*/

namespace Scan;

use \Boot\Support\Str;
use \Boot\Support\Facades\Auth;
use \setasign\Fpdi\Fpdi;

class Scan
{

    public $ABBYY_APP_ID = 'df5cf4af-2945-4ea6-acd1-9abce9b4bdc7';
    public $ABBYY_PASS = 'evyQ1bMlaf4Q4JljAi+SljQw';
    public $ABBYY_BASE_URL = 'http://cloud-eu.ocrsdk.com';
    public $allowed = ['pdf'];


    public function upload()
    {
        $result = [];
        $file = $_FILES["file"] ?? false;
        if($file) {
            $path_info = pathinfo($file['name']);
            $ext = $path_info['extension'];		
            if(in_array($ext, $this->allowed)){
                if($ext != 'pdf') {

                } else {
                    $uniqid = uniqid();
                    $uploaddirClear = 'files/scan/'.$uniqid.'/';                   
                    $uploaddir = ROOT.$uploaddirClear;                   
					if (!file_exists($uploaddir)) {
						mkdir($uploaddir, 0777);						
					}
                    $uploadfileClear = $uploaddirClear . 'source' . '.' .$ext;    
                    $uploadfile = $uploaddir . 'source' . '.' .$ext;    
                    if (move_uploaded_file($file['tmp_name'], $uploadfile)) {
                       
                    } else {
                        $result['type'] = 'error';
                        $result['error'] = 'Возможная атака с помощью файловой загрузки!';
                        return  $result;
                    }
                    $result['type'] = 'success';
                    $pageDirClear = 'files/scan/'.$uniqid.'/page/';                   
                    $pageDir = ROOT.$pageDirClear;                   
					if (!file_exists($pageDir)) {
						mkdir($pageDir, 0777);						
					}
                    $pdf = new \Spatie\PdfToImage\Pdf($uploadfile);
                    $count = $pdf->getNumberOfPages();
                    $pages = [];
                    // for ($i = 1; $i <= $count; $i++) {
                    //     $pdf->setPage($i)->setOutputFormat('png')->saveImage($pageDir);       
                    //     $pages[] =  ['page' => $i, 'value' => config('app.url').'/'. $pageDirClear.$i.'.png'];             
                    // }                   
                    $result['count'] = $count;                   
                    $result['pages'] = $pages;                   
                    $result['pdf'] = config('app.url').'/api/app/scan.pdf?uniqid'.$uniqid;                   
                    $result['source'] = config('app.url').'/'. $uploadfileClear;                   
                    $result['uniqid'] = $uniqid;                   
                    return  $result;
                }

            } else {
                $result['error'] = 'Не поддерживаемый формат файла!';
            }
        } else {
            $result['error'] = 'Не выбран файл!';
        }
        $result['type'] = 'error';
        return  $result;
    }

    public function pageCount($filename){
        $fp = fopen($filename, 'r');
        if ($fp) {
            $count = 0;
            while(!feof($fp)) {
                $line = fgets($fp,255);
                if (preg_match('|/Count [0-9]+|', $line, $matches)){
                    preg_match('|[0-9]+|', $matches[0], $matches2);
                    if ($count < $matches2[0]) {
                        $count = trim($matches2[0]); 
                    }
                } 
            }
            fclose($fp);
            return $count;
        } else {
            return false;
        }
    }

    


    // public $fileName = dirname(__FILE__).'/test2.pdf';

    public function do()
    {
        // $fileName = dirname(__FILE__) . '/1_spec (24).pdf';
        // $fileName = dirname(__FILE__) . '/test2.pdf';
        // $fileName = dirname(__FILE__) . '/12333.pdf';
             
       
        // file_put_contents(dirname(__FILE__) . '/res3.xml', $xml);
        $uniqid = $_POST["uniqid"] ?? false;
        $page = $_POST["page"] ?? null;









        
        if($uniqid) {
            $uploaddirClear = ROOT.'files/scan/'.$uniqid.'/source.pdf';  
            // $pdf = new FPDI();
            // $pageCount = $pdf->setSourceFile($uploaddirClear);

          
            
            // //  Add all pages of source to new document
            // for( $pageNo=1; $pageNo<=$pageCount; $pageNo++ )
            // {
            //     //  Skip undesired pages
            //     if( in_array($pageNo,$page) )
            //        {
            
            //     //  Add page to the document
            //     $templateID = $pdf->importPage($pageNo);
            //     $pdf->getTemplateSize($templateID);
            //     $pdf->addPage();
            //     $pdf->useTemplate($templateID);
            //        }
            // }            
            // $pdf->Output($uploaddirClear,'F');
            // exit;


            
            $xml = $this->abbyy($uploaddirClear);
           
            // $xml = file_get_contents(dirname(__FILE__) . '/res2.xml');
            $read = $this->read($xml, $page);
            // $read = $this->read($xml, $page);
            $result['type'] = 'success';
            $result['data'] = $read['find'];         
            $result['founded'] = $read['founded'];         
            return  $result;
        }
        $result['type'] = 'error';
        $result['error'] = 'Не выбран файл!';       
        return  $result;
        // dd($xml);
    }

    public function commasReplace($text)
    {
        return str_replace(',', '.', $text);
    }

    public function getPdf()
    {
        header("Content-type: application/pdf");  
        header("Access-Control-Allow-Origin: *");
        $uniqid = $_GET['uniqid'] ?? $_POST["uniqid"] ?? false;
        echo file_get_contents(ROOT.'/files/scan/'.$uniqid.'/source.pdf');
        exit;
    }

    public function read($read, $needlesPage = null)
    {
        if ($read) {
            $needlesPageArray = [];
            if(!empty($needlesPage)){
                $needlesPageArray = explode(',', $needlesPage);
            }
            $xml = simplexml_load_string($read);
            if ($xml) {
                $iterTxt = 0;
                $i = 0;
                foreach ($xml->page as $page) {
                    $i++;
                    if(empty($needlesPageArray) || in_array($i, $needlesPageArray)) {                   
                        foreach ($page as $block) {
                            switch (trim($block['blockType'])) {
                                case 'Text':
                                    if (!isset($textRows['text'])) $textRows['text'] = [];
                                    if (!isset($textRows['text'][$iterTxt])) $textRows['text'][$iterTxt] = [];
                                    $text = $block->text->par; //->line->formatting
                                    foreach ($block->text as $par) {
                                        foreach ($par->par as $text) {
                                            foreach ($text->line as $line) {
                                                $tmp = '';
                                                foreach ($line->formatting as $formatting) {
                                                    foreach ($formatting->charParams as $symb) {
                                                        $tmp .= $symb;
                                                    }
                                                }
                                                $textRows['text'][$iterTxt][] = $tmp;
                                            }
                                        }
                                        $iterTxt++;
                                    }
                                    break;
                                case 'Table':
                                    $row = $block->row;
                                    if (!isset($textRows['table'])) $textRows['table'] = [];
                                    foreach ($row as $cell) {
                                        $rowi = [];
                                        foreach ($cell as $text) {
                                            $tmp = '';
                                            $text =    $text->text->par->line;
                                            if ($text) {
                                                foreach ($text->formatting as $formatting) {
                                                    foreach ($formatting->charParams as $symb) {
                                                        $tmp .= (string)$symb;
                                                        // var_export($symb);				echo '<hr>';					
                                                    }
                                                }
                                            }
                                            $rowi[] = $tmp;
                                        }
                                        $chacker = mb_strlen(trim(implode('', $rowi)));
                                        if (empty($chacker) || $chacker < 15) {
                                            // echo "<br>".$chacker;
                                        } else $textRows['table'][] = $rowi;
                                    }
                                    break;
                                default:                                   
                                    if (!isset($textRows['other'])) $textRows['other'] = [];
                                    break;
                            }
                        }
                    }
                }
            }

            $prod = [];
            if (isset($textRows['table'])) {
                foreach ($textRows['table'] as $key => $v) {
                    $prod[$key] = [];
                    foreach ($v as $h_key => $h_value) {
                        $prod[$key][$h_key] = $h_value;
                    }
                }
            }

            $need = [2, 3, 4, 5, 6];
            $stopList = ["N п/п", "Наименование и техническая характеристика", "Tun, марка,", "Код", 'Лист', 'Проверил'];
            $countColum = 6;
            $nameColum = 1;
            //чистка списка
            $prodClear = [];
            foreach ($prod as $key => $value) {
                $flag = true;
                //проверка количества на число
                if (!empty($value[$countColum]) && !is_numeric($this->commasReplace($value[$countColum]))) {
                    $flag = false;
                }
                if (!isset($value[$countColum])) {
                    $flag = false;
                }
                //проверка на стоп слова
                foreach ($value as $k => $v) {
                    if (in_array($v, $stopList)) {
                        $flag = false;
                        break;
                    }
                }
                if ($flag) {
                    $prodClear[] = $value;
                }
            }
            $badLines = true;
            //склеивание строк
            $i = 0;
            // dd($prodClear);
            while ($badLines) {
                $i++;
                $badLines = false;
                foreach ($prodClear as $key => $value) {
                    if (empty($value[$countColum])) {
                        // if($i > 8){
                        //     dd($value);
                        // }
                        if (isset($prodClear[$key - 1])) {
                            if (!empty($value[$nameColum])) {
                                $prodClear[$key - 1][$nameColum] .= ' ' . $value[$nameColum];
                                $badLines = true;
                                unset($prodClear[$key]);
                            } else {
                                unset($prodClear[$key]);
                            }
                        } else {
                            unset($prodClear[$key]);
                        }
                    }
                }
                $prodMergeTemp = [];
                foreach ($prodClear as $key => $value) {
                    $prodMergeTemp[] = $value;
                }
                $prodClear = $prodMergeTemp;
                // if($i > 20)
                // $badLines = false;
            }
            $ii = 0;
            $complete = [];
            $founded = [];
            foreach ($prodClear as $key => $value) {
                $ii++;
                $title = $value[1] ?? null;
                $marka = $value[2] ?? null;
                $sku = $value[3] ?? null;
                $man = $value[4] ?? null;
                //поиск по базе соответствий
                $catalogSearchModel =  \Catalog_v2\Model\CatalogSearch::where('title', $title);
                if(!empty($marka)){
                    $catalogSearchModel->orWhere('marka', $marka);
                }
                if(!empty($marka)){
                    $catalogSearchModel->orWhere('sku', $sku);
                } 
                if(!empty($marka)){
                    $catalogSearchModel->orWhere('man', $man);
                }
                $catalogSearch = $catalogSearchModel->get();
                // dd();
                $maxMark = 0;
                $maxGrade = 0;
                $maxCid = 0;
                if(!empty($catalogSearch)){
                    $searchArray = [];
                    foreach ($catalogSearch as $k => $v) {
                        $grade = 0;
                        if(($v->title == $title) && (!empty($title))) { $grade ++; }
                        if(($v->marka == $marka) && (!empty($marka))) { $grade ++; }
                        if(($v->sku == $sku) && (!empty($sku))) { $grade ++; }
                        if(($v->man == $man) && (!empty($man))) { $grade ++; }
                        $searchArray[] = [
                            'cid' => $v->cid,
                            'grade' => $grade,
                            'mark' => $v->mark,
                        ];
                    }                    
                    foreach ($searchArray as $k => $v) {
                        if($maxGrade <= $v['grade'] && $v['grade'] != 0) {
                            if($maxGrade == $v['grade']) {
                                if($maxMark <= $v['mark']) {
                                    $maxCid = $v['cid'];
                                }
                            } else {
                                $maxCid = $v['cid'];
                            }
                        }
                    }
                }
                $ct = null;
                if($maxCid != 0) {
                    $ct = \Catalog_v2\Model\Catalog::find($maxCid);
                } else {
                    //поиск по каталогу 
                    $catalog = \Catalog_v2\Model\Catalog::query()->select('id', 'title', 'sku', 'spec');
                    $catalog->when(!empty($title), function ($q) use ($title) { 
                        return $q->orWhere('title', 'like', '%'.$title.'%');
                    });
                    $catalog->when(!empty($marka), function ($q) use ($marka) { 
                        return $q->orWhere('title', 'like', '%'.$marka.'%');
                    });
                    $catalog->when(!empty($sku), function ($q) use ($sku) { 
                        return $q->orWhere('sku', 'like', '%'.$sku.'%')->orWhere('sku2', 'like', '%'.$sku.'%')->orWhere('sku3', 'like', '%'.$sku.'%')->orWhere('sku_UdmurtSnab', 'like', '%'.$sku.'%');
                    });              
                    $ct = $catalog->limit('1')->first();
                }
                // var_dump(\DB::getQueryLog());
                // $catalog = \Catalog_v2\Model\Catalog::where('title', 'like', '%'.$value[1].'%')->orWhere('title', 'like', '%'.$value[2].'%')->orWhere('sku', 'like', '%'.$value[3].'%')->first();
                // var_dump($ct);
                // exit;
                $complete[] = [
                    'pos' => $ii,
                    'title' => $value[1],
                    'marka' => $value[2],
                    'sku'   => $value[3],
                    'man'   => $value[4],
                    'unit'  => $value[5],
                    'count' => $value[6],
                    'founded' => [
                        'cid' => $ct->id ?? null,
                        'title' => $ct->spec[1] ?? $ct->title ?? null,
                        'marka' => $ct->spec[2] ?? null,
                        'sku' => $ct->sku ?? null,
                        'man' => $ct->spec[3] ?? null,
                        'unit' => $value[5],
                        'count' => $value[6],
                        'maxGrade' => $maxGrade,
                        'catalogSearch' => $catalogSearch,
                        'searchArray' => $searchArray,
                        'catalogSearchModel' => $catalogSearchModel->toSql(),
                    ],
                   

                    // {"1":"Модуль присоединительный Ду 40/20/20 мм","2":"МП-РС 40/20/20","3":""}
                    
                ];
                $founded[] = [
                    'pos' => $ii,
                    'cid' => $ct->id ?? null,
                    'title' => $ct->spec[1] ?? $ct->title ?? null,
                    'marka' => $ct->spec[2] ?? null,
                    'sku' => $ct->sku ?? null,
                    'man' => $ct->spec[3] ?? null,
                    'unit' => $value[5],
                    'count' => $value[6],
                   
                ];
            }
            
            // 
            // 5 => "Завод-"
            // 6 => "Единица"
            // 7 => "Коли¬"
            // 8 => "Масса"
            // 9 => "Примечания"]

            return ['find' => $complete, 'founded' => $founded];


            
        }
    }

    public function abbyy($fileName)
    {
        $url = $this->ABBYY_BASE_URL . '/processImage?language=russian,english&exportFormat=xml';
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_USERPWD, $this->ABBYY_APP_ID . ":" . $this->ABBYY_PASS);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        curl_setopt($curlHandle, CURLOPT_USERAGENT, "Atri Energo Recognizer");
        curl_setopt($curlHandle, CURLOPT_FAILONERROR, true);
        $post_array = ["my_file" => new \CURLFile($fileName)];
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $post_array);
        $response = curl_exec($curlHandle);
        if ($response == FALSE) {
            $errorText = curl_error($curlHandle);
            curl_close($curlHandle);
            die($errorText);
        }
        $httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        curl_close($curlHandle);

        // Parse xml response
        $xml = simplexml_load_string($response);
        if ($httpCode != 200) {
            if (property_exists($xml, "message")) {
                die($xml->message);
            }
            die("unexpected response " . $response);
        }

        $arr = $xml->task[0]->attributes();
        $taskStatus = $arr["status"];
        if ($taskStatus != "Queued") {
            die("Unexpected task status " . $taskStatus);
        }

        $taskid = $arr["id"];
        $url = $this->ABBYY_BASE_URL . '/getTaskStatus';
        if (empty($taskid) || (strpos($taskid, "00000000-0") !== false)) {
            die("Invalid task id used when preparing getTaskStatus request");
        }
        $qry_str = "?taskid=$taskid";

        while (true) {
            sleep(5);
            // echo '*';
            $curlHandle = curl_init();
            curl_setopt($curlHandle, CURLOPT_URL, $url . $qry_str);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlHandle, CURLOPT_USERPWD, $this->ABBYY_APP_ID . ":" . $this->ABBYY_PASS);
            curl_setopt($curlHandle, CURLOPT_USERAGENT, "Atri Energo Recognizer");
            curl_setopt($curlHandle, CURLOPT_FAILONERROR, true);
            $response = curl_exec($curlHandle);
            $httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
            curl_close($curlHandle);

            // parse xml
            $xml = simplexml_load_string($response);
            if ($httpCode != 200) {
                if (property_exists($xml, "message")) {
                    die($xml->message);
                }
                die("Unexpected response " . $response);
            }
            $arr = $xml->task[0]->attributes();
            $taskStatus = $arr["status"];
            if ($taskStatus == "Queued" || $taskStatus == "InProgress") {
                // continue waiting
                continue;
            }
            if ($taskStatus == "Completed") {
                // exit this loop and proceed to handling the result
                break;
            }
            if ($taskStatus == "ProcessingFailed") {
                die("Task processing failed: " . $arr["error"]);
            }
            die("Unexpected task status " . $taskStatus);
        }

        $url = $arr["resultUrl"];
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curlHandle);
        curl_close($curlHandle);
        return $response;
    }

    public function save($uid)
    {
        if(isset($_POST['pid']) && isset($_POST['module']) && !empty($_POST['products'])){
            $module = $_POST['module'];
            $pid = $_POST['pid'];
            $products = $_POST['products'];
            $detail = [];
            foreach ($products as $key => $value) {
                $v = json_decode($value, true);
                if(isset($v['newCid'])) {
                    $detail[] = [
                        'cid' => $v['newCid'],
                        'count' => $v['count'],
                    ];
                    $search = [
                        'title' => $v['title'] ?? null,
                        'marka' => $v['marka'] ?? null,
                        'sku' => $v['sku'] ?? null,
                        'man' => $v['man'] ?? null,
                        'cid' => $v['newCid'],
                    ];
                    $cs = \Catalog_v2\Model\CatalogSearch::firstOrCreate($search);
                    $cs->increment('mark');
                }
               
            }
            switch ($module) {
                case '0':
                case 'sales':
                    if($pid == 0) {
                        $request = array(
                           'uid' => null,
                           'object' => $_POST['object_id'] ?? null,
                           'oid' => $_POST['org_id'] ?? 507,
                           'org' => '1',
                           'nds_includes' => '1',
                           'stock_id' => '1',
                           'uid_send' => 0
                        );
                        $sales = \Sales\Model\Sales::create($request);
                        $SalesUsers = new \Sales\Model\SalesUsers;
                        $SalesUsers->sales_id = $sales->id;
                        $SalesUsers->type = "CLIENT";
                        $SalesUsers->uid = $uid;
                        $SalesUsers->save();
                    } else {
                        $sales = \Sales\Model\Sales::find($pid);
                    }  
                    $rd = [
                        'id' => $sales->id,
                        'items' => $detail,
                    ];
                   $this->victoria_order_tovar_add($rd);
                break;
            }
        }
        //  var_dump();   
    }


    public function victoria_order_tovar_add($request)
    {
        $id = isset($request["id"]) ? $request["id"] : false;
        $items = isset($request["items"]) ? $request["items"] : [];
        if ($id && count($items) > 0) {
            $add = [];
            $add["sales_id"] = $id;
            $add["sales_detail"] = [];
            $oid = find_org_uid();
            foreach ($items as $item) {
                $price_data = \Price::getPrice($item["cid"], ["oid" => $oid]);
                $s_price = $price_data->price_list_;
                $add["sales_detail"][] = [
                    "cid" =>$item["cid"],
                    "count" => $item["count"],
                    "discount" => 0,
                    "s_price" => $s_price,
                ];
            }
            \Sales\Support\SalesDetailSupport::mass_add($add);
        }
    }

}
