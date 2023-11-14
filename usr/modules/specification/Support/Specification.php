<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Specification\Support;

use DOMDocument;
use Firebase\JWT\JWT;
use Boot\Support\Office;
use Boot\Support\Facades\Http;
use Boot\System\Office\Word\TemplateProcessor;
use Specification\Collectors\BoilerBidCollector;

class Specification
{
    protected static $accept_type = ['sales', 'boiler', 'boiler_bid', 'boiler_bid_all'];
    protected static $generate_path;
    protected static $specification_data = [];
    protected static $specification_title = null;

    public static function get($id, string $type = null, $format = null, $output = null)
    {
        if (!in_array($type, static::$accept_type)) {
            throw new \Exception("Значение поля type (" . $type . ") недопустимо!");
        }
        switch ($type) {
            default:
                // static::get_catalog($id, $type);
                // static::generate($id, $type);
                break;
            case "boiler_bid_all":
                if (!is_array($id)) {
                    $id = [$id];
                }
                $merge = Office::merge();
                foreach ($id as $value) {
                    static::get_catalog($value, $type);
                    $generate_file = static::generate();
                    $merge->addString(static::convert_pdf($generate_file));
                    unlink($generate_file);
                }
                
                $merge->merge();
                echo $merge->output(module_path('Resources' . DIRECTORY_SEPARATOR . 'Temp' . DIRECTORY_SEPARATOR . uniqid("merge_") . '.pdf'));
                break;
        }
    }

    protected static function get_catalog($id, string $type = null)
    {
        switch ($type) {
            case "boiler_bid_all":
                static::$specification_data = BoilerBidCollector::get($id);
                static::$specification_title = BoilerBidCollector::get_title($id);
                break;
        }
    }

    protected static function generate()
    {
        $temp_file = module_path('Resources' . DIRECTORY_SEPARATOR . 'Temp' . DIRECTORY_SEPARATOR . uniqid("temp_") . '.docx');
        $document = new TemplateProcessor(module_path('Resources' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'specification.docx'));
        $document->cloneRowAndSetValues('numb', static::$specification_data);
        $document->setValue('specification_title', static::$specification_title);
        $document->saveAs($temp_file);

        $document = new TemplateProcessor($temp_file);
        $document->setValue('pcount', static::get_page_count($temp_file));
        $document->saveAs($temp_file);
        return $temp_file;
    }

    protected static function get_page_count($file)
    {
        $temp_file = module_path('Resources' . DIRECTORY_SEPARATOR . 'Temp' . DIRECTORY_SEPARATOR . uniqid("temp_page_") . '.pdf');
        file_put_contents($temp_file, static::convert_pdf($file));
        $fp = fopen($temp_file, 'r');
        if ($fp) {
            $count = 0;
            while (!feof($fp)) {
                $line = fgets($fp, 255);
                if (preg_match('|/Count [0-9]+|', $line, $matches)) {
                    preg_match('|[0-9]+|', $matches[0], $matches2);
                    if ($count < $matches2[0]) {
                        $count = trim($matches2[0]);
                    }
                }
            }
            fclose($fp);
            unlink($temp_file);
            return $count;
        } else {
            throw new \Exception("Невозможно открыть PDF файл для подсчета страниц!");
        }
    }


    protected static function convert_pdf($file)
    {
        $uniqid = uniqid() . '.docx';
        Http::withToken('eJLttiTlhKRbbZDDwSu3WWfvdLQM5Ttp')->attach('attachment', file_get_contents($file), $uniqid)->post('https://api.ae-work.ru/storage.php');
        $payload = [
            "async" => false,
            "filetype" =>  "docx",
            "key" =>  uniqid(),
            "outputtype" => "pdf",
            "title" =>  "Example Document Title.docx",
            "url" =>  "https://api.ae-work.ru/uploads/" . $uniqid,
        ];
        $token = JWT::encode($payload, 'eJLttiTlhKRbbZDDwSu3WWfvdLQM5Ttp');
        $res = Http::withToken($token)->acceptJson()->post('http://ae-work.ru:1505/ConvertService.ashx', $payload)->json();
        return file_get_contents($res['fileUrl']);
    }
}

// function specification($id = null, $type = null)
// {
//     global $db, $config, $core, $tags, $url, $headTag, $cache, $lang;
//     $info = $core->tpl->info('Внимание! Данный раздел находится в разработке и возможно не точное отображение информации. Для получения актуальных данных обратиться к нашим менеджерам по телефону +7 (3412) 908-123 и мы обязательно поможем Вам!', 'warning-2', false, false, false, false, false, true);

//     if ((isset($_COOKIE['cart_products']) && !empty($_COOKIE['cart_products'])) || !empty($id)) {
//         $ids_str = '';
//         $flag = true;
//         if (empty($id)) {
//             $cart_products = json_decode($_COOKIE['cart_products'], true);
//             //print_r($cart_products);
//             $num_array = array();
//             foreach ($cart_products as $key => $value) {
//                 $id = $key;
//                 $ids_str .= "'" . $key . "', ";
//                 $num_array[$id] = $value;
//             }
//         } else {
//             switch ($type) {
//                 default:

//                     break;

//                 case "sales":
//                     $query = $db->query("SELECT n.*, u.unit, u.sname as unit_sname, m.name as manufacture_name, m.id as manufacture_id, sd.count
// 					FROM `" . DB_PREFIX . "_sales_detail` as sd
// 					LEFT JOIN `" . DB_PREFIX . "_catalog` as n ON(sd.cid = n.id)
// 					LEFT JOIN `" . DB_PREFIX . "_catalog_unit` as u on(u.unit=n.unit)
// 					LEFT JOIN `" . DB_PREFIX . "_catalog_manufacture` as m on(m.id = n.man)
// 					WHERE sd.sales_id = '" . $id . "' AND sd.del = '0' ORDER BY sd.position");
//                     $flag = false;
//                     /*
// 						$query = $db->query("SELECT cid, count FROM ".DB_PREFIX."_sales_detail WHERE sales_id = '".$id."'");
// 						if($db->numRows($query) > 0)
// 						{
// 							while($sales = $db->getRow($query))
// 							{
// 								$cid = $sales['cid'];
// 								$ids_str .= "'".$cid."', ";
// 								$num_array[$cid] = $sales['count'];
// 							}
// 						}
// 						*/
//                     break;
//                 case "boiler":
//                     $query = $db->query("SELECT n.*, u.unit, u.sname as unit_sname, m.name as manufacture_name, m.id as manufacture_id, sd.count
// 						FROM `" . DB_PREFIX . "_boiler_bid_data_detail` as sd
// 						LEFT JOIN `" . DB_PREFIX . "_boiler_bid_data` as bbd ON(bbd.id = sd.bbd_id)
// 						LEFT JOIN `" . DB_PREFIX . "_boiler_bid_type` as bbt ON(bbd.bid_type_id = bbt.id)
// 						LEFT JOIN `" . DB_PREFIX . "_boiler_bid` as bb ON(bb.id = bbt.bid_id)
// 						LEFT JOIN `" . DB_PREFIX . "_catalog` as n ON(sd.cid = n.id)
// 						LEFT JOIN `" . DB_PREFIX . "_catalog_unit` as u on(u.unit=n.unit)
// 						LEFT JOIN `" . DB_PREFIX . "_catalog_manufacture` as m on(m.id = n.man)
// 						WHERE bb.id = '" . $id . "' AND sd.checkbox = 1
// 						ORDER BY bbt.position, bbd.position, sd.position");
//                     $flag = false;
//                     break;
//                 case "boiler_bid":
//                 case "boiler_bid_all":
//                     $query = $db->query("SELECT n.*, u.unit, u.sname as unit_sname, m.name as manufacture_name, m.id as manufacture_id, sd.count
// 						FROM `" . DB_PREFIX . "_boiler_bid_data_detail` as sd
// 						LEFT JOIN `" . DB_PREFIX . "_catalog` as n ON(sd.cid = n.id)
// 						LEFT JOIN `" . DB_PREFIX . "_catalog_unit` as u on(u.unit=n.unit)
// 						LEFT JOIN `" . DB_PREFIX . "_catalog_manufacture` as m on(m.id = n.man)
// 						WHERE sd.bbd_id = '" . $id . "'
// 						ORDER BY sd.position");
//                     $flag = false;
//                     break;
//                 case "selection":
//                     $query = $db->query("SELECT n.*,  u.unit, u.sname as unit_sname, m.name as manufacture_name, m.id as manufacture_id, sd.count FROM " . DB_PREFIX . "_selection_detail as sd LEFT JOIN " . DB_PREFIX . "_catalog as n ON(sd.cid = n.id) LEFT JOIN " . DB_PREFIX . "_catalog_unit as u on(u.unit=n.unit) LEFT JOIN " . DB_PREFIX . "_catalog_manufacture as m on(m.id = n.man) WHERE sd.selection_id = '" . $id . "' ORDER BY position");
//                     $flag = false;

//                     break;

//                 case "metering":
//                     $query = $db->query("SELECT n.*,  u.unit, u.sname as unit_sname, m.name as manufacture_name, m.id as manufacture_id, sd.count FROM " . DB_PREFIX . "_metering_detail as sd LEFT JOIN " . DB_PREFIX . "_catalog as n ON(sd.cid = n.id) LEFT JOIN " . DB_PREFIX . "_catalog_unit as u on(u.unit=n.unit) LEFT JOIN " . DB_PREFIX . "_catalog_manufacture as m on(m.id = n.man) WHERE sd.metering_id = '" . $id . "' ORDER BY position");
//                     $flag = false;

//                     break;

//                 case "metering_scheme":
//                     $query = $db->query("SELECT n.*,  u.unit, u.sname as unit_sname, m.name as manufacture_name, m.id as manufacture_id, sd.count FROM " . DB_PREFIX . "_metering_scheme_detail as sd LEFT JOIN " . DB_PREFIX . "_catalog as n ON(sd.cid = n.id) LEFT JOIN " . DB_PREFIX . "_catalog_unit as u on(u.unit=n.unit) LEFT JOIN " . DB_PREFIX . "_catalog_manufacture as m on(m.id = n.man) WHERE sd.scheme_id = '" . $id . "' ORDER BY position");
//                     $flag = false;

//                     break;


//                 case "bill":

//                     break;

//                 case "specification":
//                     $query = $db->query("SELECT n.*, u.unit, u.sname as unit_sname, m.name as manufacture_name, m.id as manufacture_id, sd.count
// 						FROM `" . DB_PREFIX . "_specification_detail` as sd
// 						LEFT JOIN `" . DB_PREFIX . "_catalog` as n ON(sd.cid = n.id)
// 						LEFT JOIN `" . DB_PREFIX . "_catalog_unit` as u on(u.unit=n.unit)
// 						LEFT JOIN `" . DB_PREFIX . "_catalog_manufacture` as m on(m.id = n.man)
// 						WHERE sd.specification_id = '" . $id . "' AND sd.deleted_at IS NULL ORDER BY sd.position");
//                     $flag = false;
//                     break;
//             }
//         }
//         if ($flag) {
//             $query = $db->query("SELECT n.*,  u.unit, u.sname as unit_sname, m.name as manufacture_name, m.id as manufacture_id  FROM " . DB_PREFIX . "_catalog as n LEFT JOIN " . DB_PREFIX . "_catalog_unit as u on(u.unit=n.unit) LEFT JOIN " . DB_PREFIX . "_catalog_manufacture as m on(m.id = n.man) WHERE n.id IN(" . substr($ids_str, 0, -2) . ") ORDER BY title ASC");
//         }
//         $items = '';
//         $spec_data = array(
//             'num' => array(),
//             'sku' => array(),
//             'unit' => array(),
//             'name' => array(),
//             'sum' => array(),
//             'model' => array(),
//             'manufacter' => array(),
//             'weight' => array()
//         );
//         if ($db->numRows($query) > 0) {
//             $i = 0;
//             while ($catalog = $db->getRow($query)) {
//                 $i++;
//                 $sku = $catalog['sku'];
//                 $unit = $catalog['unit_sname'];
//                 // $name = "";
//                 $name = html_entity_decode(htmlspecialchars_decode($catalog['title'], ENT_QUOTES), ENT_QUOTES);
//                 $model = ' ';
//                 $manufacter = (!empty($catalog['manufacture_name']) ? $catalog['manufacture_name'] : ' ');
//                 $spec = json_decode($catalog['spec'], true);
//                 $weight     = json_decode($catalog['weight'], true);
//                 if (is_array($spec)) {
//                     if (!empty($spec['1'])) {
//                         $name = html_entity_decode(htmlspecialchars_decode($spec['1'], ENT_QUOTES), ENT_QUOTES);
//                     }
//                     if (!empty($spec['2'])) {
//                         $model = str_replace("&lt;br&gt;", "<w:br/>", $spec['2']);
//                         $model = str_replace("&amp;lt;br&amp;gt;;", "<w:br/>", $model);
//                     }
//                     if (!empty($spec['3'])) {
//                         $manufacter = $spec['3'];
//                     }
//                 }

//                 $weight_good = '';
//                 if (is_array($weight)) {
//                     if (!empty($weight['1'])) {
//                         $weight_good = $weight['1'];
//                     }
//                 }


//                 if (!$flag) {
//                     $num_array[$catalog['id']] = $catalog['count'];
//                 }

//                 $name = str_replace("<", "&lt;", $name);
//                 $name = str_replace(">", "&gt;", $name);

//                 array_push($spec_data['num'], $i);
//                 array_push($spec_data['sku'], $sku);
//                 array_push($spec_data['unit'], $unit);
//                 array_push($spec_data['name'], $name);
//                 array_push($spec_data['sum'], $num_array[$catalog['id']]);
//                 array_push($spec_data['model'], $model);
//                 array_push($spec_data['manufacter'], $manufacter);
//                 array_push($spec_data['weight'], $weight_good);
//             }
//         }
//         require_once(ROOT . 'usr/plugins/PHPWord.php');

//         $PHPWord = new PHPWord();

//         $document = $PHPWord->loadTemplate(ROOT . 'files/tpl/specification.docx');

//         $Month_rus = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];

//         /*
// 			$document->setValue('{day}', date('d'));
// 			$document->setValue('{month}', $Month_rus[ date( "n" ) - 1 ]);
// 			$document->setValue('{year}', date('Y'));

// 			$document->setValue('{order}', $order_id);

// 			$document->setValue('{worker}', $user_data["surname"] ." ". $user_data["name"] ." ". $user_data["ochestvo"]);

// 			$document->setValue('{e_id}', $order_data["elevator_id"]);
// 			$document->setValue('{e_address}', $elevator_data["address"]);*/



//         $document->cloneRow('spec', $spec_data);

//         //unset($_COOKIE['basket']);







//         //Load temp file
//         //$document_pdf = $PHPWord->load(ROOT.'/files/user/'.$uid .'_spec.docx');
//         //$xmlWriter = $PHPWord->createWriter($phpWord , 'PDF');
//         //$xmlWriter->save('files/user/'.$uid .'_spec.pdf');
//         //echo  '<a href="files/user/'.$uid .'_spec.pdf" target="_blank" > Скачать </a>';

//         $uid = $core->auth->user_info['id'];
//         if ($type != "boiler_bid_all") {

//             $document->save(ROOT . 'files/user/' . $uid . '_spec.docx');
//             $items = '<a href="files/user/' . $uid . '_spec.docx" target="_blank" > Скачать </a>';
//             OfficeSupport::get_file(ROOT . 'files/user/' . $uid . '_spec.docx');
//             exit;
//         } else {
//             $document->save(ROOT . 'files/user/' . $uid . '_' . $id . '_spec.docx');
//             return ROOT . 'files/user/' . $uid . '_' . $id . '_spec.docx';
//         }

//         // header('Location: https://atri-energo.ru/files/user/'.$uid .'_spec.docx');


//     } else {
//         $info = $core->tpl->info('Внимание! Корзина пуста, чтобы составить спецификацию необходимо заполнить корзину необходимыми товарами!', 'error-2', false, false, false, false, false, true);
//     }


//     /*
// 	$page_title = "Корзина";
// 	$core->tpl->loadFile('catalog/catalog.carcase');
// 	$f_cart = 'yes';
// 	$f_list = '';
// 	$f_cats = '';
// 	include('carcase_tag.php');
// 	$core->tpl->end();*/
// }
