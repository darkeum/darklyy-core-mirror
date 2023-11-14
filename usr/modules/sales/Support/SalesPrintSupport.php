<?php

namespace Sales\Support;

use Boot\System\Office\OfficeSupport;
use Boot\System\Office\Spreadsheet\IOFactory;
use Boot\System\Office\Spreadsheet\Worksheet\Drawing;

class SalesPrintSupport
{
    static function print_sales($id, $type, $options)
    {
        $request = [];
        $request["id"] = $id;
        $request["module"] = "sales";
        $order = \Sales\Model\Sales::find($id)->toArray();
        $request["date"] = $order["date"];
        $request["org"] = $order["org"];
        $request["oid"] = $order["oid"];
        $request["object"] = $order["object"];
        $order_data = [];
        $order_data_with_link = [];
        $query = \Sales\Model\SalesDetail::with("catalog")->where("sales_id", $id)->orderBy("position");
        $org = getOrgInfo($order['org']);
        $org_nds = \Price::getNds($org['org']['nds']);
        if ($query->exists()) {
            $i = 0;
            $total_all = 0;
            $nds_all = 0;
            foreach ($query->get() as $sd) {
                $i++;
                $stable_name = $sd->s_name;
                $stable_name = ee($stable_name);
                $stable_name = ee($stable_name);
                $stable_name = str_replace("&", "&amp;", $stable_name);
                $stable_name = str_replace("<", "&lt;", $stable_name);
                $stable_name = str_replace(">", "&gt;", $stable_name);
                $stable_sku = $sd->s_sku;
                $stable_unit = $sd->catalog->unit;
                $sum = $sd->count;
                $price = round(($sd->total / $sd->count), 2);
                $total_nds = $sd->total;

                if ($org_nds != '0') {
                    $nds = round(($total_nds / 120 * 20), 2);
                    $total = $total_nds - $nds;
                    $total_all = $total_all + round($total, 2);

                    $price_with_nds = round(($sd->total / $sd->count), 2);
                    $price = round($price_with_nds - ($price_with_nds / 120 * 20), 2);

                    $nds_all += $nds;
                } else {
                    $price = round(($sd->total / $sd->count), 2);
                    $price_with_nds = $price;
                    $total = $total_nds;
                    $total_all = $total_all + round($total, 2);
                    $nds = 0;

                    $nds_all += $nds;
                }
                $lcd = \Sales\Model\SalesDetail::getLocalDateDelivery();
                $date_post = empty($sd->local_date_delivery) ? '' : $sd->local_date_delivery;
                $date_post = isset($lcd[$sd->local_date_delivery]) ? $lcd[$sd->local_date_delivery] : '';
                $order_data[] =  array(
                    'n'                    => $i,
                    'sku'                 => $stable_sku,
                    'name'                 => $stable_name,
                    's'                 => $sum,
                    'u'                 => get_unit('data', $stable_unit),
                    'price'             => number_format($price, 2, ',', ''),
                    'price_with_nds'     => number_format($price_with_nds, 2, ',', ''),
                    'nds'                 => number_format($nds, 2, ',', ''),
                    'total'             => number_format($total, 2, ',', ''),
                    'total_with_nds'     => number_format($total_nds, 2, ',', ''),
                    'time'                 => $date_post,
                    'space'                 => '',
                );

                $order_data_with_link[] =  array(
                    'n'                    => $i,
                    'sku'                 => $stable_sku,
                    'name'                 => array(
                        'text'  => $stable_name,
                        'link'  => config('app.url').'/catalog_v2/view/' . $sd->cid,
                        'style' => array('color' => '005cc5', 'underline' => \Boot\System\Office\Word\Style\Font::UNDERLINE_FAKE)
                    ),
                    's'                 => $sum,
                    'u'                 => get_unit('data', $stable_unit),
                    'price'             => number_format($price, 2, ',', ''),
                    'price_with_nds'     => number_format($price_with_nds, 2, ',', ''),
                    'nds'                 => number_format($nds, 2, ',', ''),
                    'total'             => number_format($total, 2, ',', ''),
                    'total_with_nds'     => number_format($total_nds, 2, ',', ''),
                    'time'                 => $date_post,
                    'space'                 => '',
                );
            }
        }
        $request["order_data"] = $order_data;
        $request["order_data_with_link"] = $order_data_with_link;
        $request["total"] = $order["total"];
        $request["nds"] = $nds_all;
        $request["uid"] = $order["uid"];
        $request["doc_numb"] = $order["numb"];
        $request["format"] = isset($options["format"]) ? $options["format"] : '';
        $request["type"] = $type;
        $request["autoprint"] = isset($options["autoprint"]) ? $options["autoprint"] : '';

        static::universal_print($request);
    }

    static function universal_print($request)
    {
        //["id" => "", "module" => "", "date" => "", "org" => "", "oid" => "", "total" => "", "nds" => "", "order_data" => [], "uid" => "", "doc_numb" => "", "format" => "", "type" => "", "autoprint" => ""]
        // $uid = $core->auth->user_info['id'];	
        $id = intval($request["id"]);
        $module = empty($request["module"]) ? "sales" : $request["module"];
        $type = empty($request["type"]) ? null : $request["type"];
        $dir = ROOT . 'files/' . $module . '/' . $id;
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $date = strtotime($request['date']);
        $org_id = empty($request["org"]) ? 1 : $request["org"];
        $org = getOrgInfo($org_id);
        $oid = \Organizations\Model\Organizations::findOrFail($request["oid"])->toArray();
        $oid_flag = isset($oid['inn']);
        // if(isset($oid['inn'])&&($org != false))
        if (($org != false)) {
            $tag_array_value = array();
            //сбор данных
            $org_nds = \Price::getNds($org['org']['nds']);
            $org_nds_id = $org_nds;
            if ($org_nds != '0') {
                $org_nds_view = $org_nds . '%';
                $org_nds_total_view = 'с НДС';
                $nds_down = ", в том числе НДС " . $org_nds . "%:";
                $st = '1';
            } else {
                $org_nds_view = 'Без НДС';
                $org_nds_total_view = 'без НДС';
                $nds_down = "";
                $st = '2';
            }
            $Month_rus = getMonthRus();
            $order_data = $request["order_data"];
            $request["purchase_total"] = 0;
            $request["purchase_nds"] = 0;
            foreach ($order_data as $key => $item) {
                $order_data[$key]["price_clear"] = (float) str_replace(",", ".", $item["price"]);
                $order_data[$key]["total_clear"] = (float) str_replace(",", ".", $item["total"]);
                $order_data[$key]["nds_clear"] = (float) str_replace(",", ".", $item["nds"]);

                $order_data[$key]["purchase_price"] = $order_data[$key]["purchase_price"] ?? 0;
                $pp = \Price::get_all(["price" => $order_data[$key]["purchase_price"], "count" => $order_data[$key]["s"]]);
                $order_data[$key]["purchase_total"] = $pp["total"];
                $order_data[$key]["purchase_nds"] = $pp["nds"];
                $request["purchase_total"] += $pp["total"];
                $request["purchase_nds"] += $pp["nds"];
            }
            if (isset($request["order_data_with_link"])) {
                $order_data_with_link = $request["order_data_with_link"];
                foreach ($order_data_with_link as $key => $item) {
                    $order_data_with_link[$key]["price_clear"] = (float) str_replace(",", ".", $item["price"]);
                    $order_data_with_link[$key]["total_clear"] = (float) str_replace(",", ".", $item["total"]);
                    $order_data_with_link[$key]["nds_clear"] = (float) str_replace(",", ".", $item["nds"]);
                }
            }



            if ($org['org']['nds'] == '0') {
                $nds_includes = 'Без налога (НДС):';
                $total_with_nds = '';
                $total_with_nds_sum = number_format($request['total'], 2, ',', ' ');
                $nds_sum = '-';
            } else {
                $nds_includes = 'В том числе НДС (' . \Price::getNds($org['org']['nds']) . '%):';
                $total_with_nds = 'Итого с НДС';
                $total_with_nds_sum = number_format($request['total'], 2, ',', ' ');
                $nds_sum = number_format($request['nds'], 2, ',', ' ');
            }
            $total_without_nds_ = $request["total"] - $request['nds'];
            $total_without_nds = number_format($total_without_nds_, 2, ',', ' ');

            $manager_name = '';
            $manager_phone = '';
            $manager_email = '';
            if (!empty($request['uid'])) {
                $user = \Boot\Models\Users::findOrFail($request['uid'])->toArray();

                $manager_name = $user["short_user_name"];
                $manager_email = $user['email'];
                $org_phone = $org['org']['phone'];
                $manager_phone = (!empty($user['phone'])) ? $user['phone'] . ' ' . $org_phone : $org_phone;
                $rel = \Boot\Models\UsersOrgRelation::where('uid', $request['uid'])->where('org', $org_id)->first();
                if (!empty($rel)) {
                    if (!empty($rel->email)) {
                        $manager_email = $rel->email;
                    }
                    if (!empty($rel->phone)) {
                        $manager_phone = $rel->phone;
                    }
                }
            }
            $total_rus = str_replace("(", "", num2str($request['total']));
            $total_rus = str_replace(")", "", $total_rus);

            $tag_array_value['doc_numb'] = $request['doc_numb'];
            $tag_array_value['day'] = date('d', $date);
            $tag_array_value['month'] = $Month_rus[date("n", $date) - 1];
            $tag_array_value['year'] = date('Y', $date);
            $tag_array_value['doc_full'] = '№' . $tag_array_value['doc_numb'] . ' от ' . $tag_array_value['day'] . ' ' . $tag_array_value['month'] . ' ' . $tag_array_value['year'];
            $tag_array_value['org_name'] = htmlspecialchars_decode($org['organizations']['name']);
            $tag_array_value['org_inn'] = $org['organizations']['inn'];
            $tag_array_value['org_kpp_only']  = ($org['organizations']['type'] == "INDIVIDUAL") ? '' : $org['organizations']['kpp'];
            $tag_array_value['org_kpp']  = ($org['organizations']['type'] == "INDIVIDUAL") ? '' : 'КПП:' . $org['organizations']['kpp'] . ',';
            $tag_array_value['org_bank_address'] = $org['organizations_bank']['address'];
            $tag_array_value['org_bank_name'] = $org['organizations_bank']['name'];
            $tag_array_value['org_bank_bik'] = $org['organizations_bank']['bik'];
            $tag_array_value['org_bank_ks'] = $org['organizations_bank']['kschet'];
            $tag_array_value['org_bank_rs'] = $org['organizations_bank']['rschet'];
            $tag_array_value['org_address'] = $org['organizations']['yaddress'];
            $tag_array_value['org_full'] = $tag_array_value['org_name'] . ', ИНН:' . $tag_array_value['org_inn'] . ', ' . $tag_array_value['org_kpp'] . ' ' . $tag_array_value['org_address'];
            $tag_array_value['org_full_kp'] = 'Компания ' . $tag_array_value['org_name'] . ' предлагает Вам ознакомиться с предложением по следующим позициям:';
            $tag_array_value['org_full_ks2'] = "{$tag_array_value['org_name']} {$tag_array_value['org_address']}";
            $tag_array_value['org_name_director'] = "Директор {$tag_array_value['org_name']}";
            $promotions = \Promotions\Models\Promotions::where('date_start', '<=', date('Y-m-d'))->where('date_end', '>=', date('Y-m-d'))->where('status', '=', 1)->whereNotNull('preview_doc')->where('preview_doc', '!=', '')->inRandomOrder()->first();
            $promotions_preview = '';
            if (!empty($promotions)) {
                $promotions_preview = ROOT . $promotions->preview_doc;
            }
            // dd($promotions_preview);
            $tag_array_value['sale'] = $promotions_preview;
            $tag_array_value['promotions_url'] = $promotions->url ?? "";
            $tag_array_value['logo'] = ROOT . $org['org']['logo'];
            $tag_array_value['sign'] = ROOT . $org['org']['bill_sign'];
            if ($oid_flag) {
                $tag_array_value['oid_name'] =  htmlspecialchars_decode($oid['name']);
            } else {
                $tag_array_value['oid_name'] = "";
            }
            if ($oid_flag) {
                $tag_array_value['oid_name_director'] =  "Директор {$tag_array_value['oid_name']}";
            } else {
                $tag_array_value['oid_name_director'] = "";
            }
            if ($oid_flag) {
                $tag_array_value['oid_inn'] =  $oid['inn'];
            } else {
                $tag_array_value['oid_inn'] = "";
            }
            if ($oid_flag) {
                $tag_array_value['oid_address'] =  $oid['yaddress'];
            } else {
                $tag_array_value['oid_address'] = "";
            }
            if ($oid_flag) {
                $tag_array_value['oid_kpp'] = ($oid['type'] == "INDIVIDUAL") ? '' : 'КПП:' . $oid['kpp'] . ',';
            } else {
                $tag_array_value['oid_kpp'] = "";
            }
            if ($oid_flag) {
                $tag_array_value['oid_full'] = $tag_array_value['oid_name'] . ', ИНН:' . $tag_array_value['oid_inn'] . ', ' . $tag_array_value['oid_kpp'] . ' ' . $tag_array_value['oid_address'];
            } else {
                $tag_array_value['oid_full'] = "";
            }
            if ($oid_flag) {
                $tag_array_value['oid_full_ks2'] = "{$tag_array_value['oid_name']} {$tag_array_value['oid_address']}";
            } else {
                $tag_array_value['oid_full_ks2'] = "";
            }
            $tag_array_value['total'] = number_format($request["total"], 2, ',', ' ');
            $tag_array_value['purchase_total'] = number_format($request["purchase_total"], 2, ',', ' ');
            $tag_array_value['total_clear'] = $tag_array_value['total'];
            $tag_array_value['total_base'] = number_format($request['total'], 2, ',', ' ');
            $tag_array_value['nds'] = $nds_sum;
            $tag_array_value['purchase_nds'] = number_format($request["purchase_nds"], 2, ',', ' ');
            $tag_array_value['nds_clear'] = $tag_array_value['nds'];
            $tag_array_value['total_with_nds_sum'] = $total_with_nds_sum;
            $tag_array_value['nds_includes'] = $nds_includes;
            $tag_array_value['total_with_nds'] = $total_with_nds;
            $tag_array_value['total_without_nds'] = $total_without_nds;
            $tag_array_value['total_without_nds_clear'] =  $tag_array_value['total_without_nds'];
            $tag_array_value['org_director'] = shortUserName($org['org']['director']);
            $tag_array_value['org_accountant'] = shortUserName($org['org']['accountant']);
            $tag_array_value['manager_name'] = $manager_name;
            $tag_array_value['manager_email'] = $manager_email;
            $tag_array_value['manager_phone'] = $manager_phone;
            $tag_array_value['total_rus'] = mb_ucfirst($total_rus);
            $count = count($order_data);
            $tag_array_value['total_full'] = 'Всего наименований ' . $count . ', на сумму ' . $tag_array_value['total_with_nds_sum'] . ' руб.';
            $tag_array_value['object_name'] = "";
            if (!empty($request["object"])) {
                $obj = \Object\Model\ObjectModel::find($request["object"]);
                if (!empty($obj)) {
                    $tag_array_value['object_name'] = ee($obj->name);
                }
            }

            $path = ROOT . "usr/modules/sales/resources/templates/";
            if (isset($request['format']) && $request['format'] == 'xlsx') {
                switch ($type) {
                    default:
                        $tpl = $path . 'sales_print.xlsx';
                        $list_row = 13;
                        $last_cord = 'AR';
                        $file_title = 'Заказ клиента';
                        break;
                    case "sales":
                        $tpl = $path . 'sales_print.xlsx';
                        $list_row = 13;
                        $last_cord = 'AR';
                        $file_title = 'Заказ клиента';
                        break;
                    case "bill":
                        $tpl = $path . 'bill_print.xlsx';
                        $list_row = 25;
                        $last_cord = 'AR';
                        $file_title = 'Счет на оплату';
                        break;
                    case "bill_sign":
                        $tpl = $path . 'bill_print_sign.xlsx';
                        $last_cord = 'AR';
                        $list_row = 25;
                        $file_title = 'Счет на оплату с факсимиле';
                        break;
                    case "kp":
                        $tpl = $path . 'kp_print.xlsx';
                        $last_cord = 'AR';
                        $list_row = 10;
                        $file_title = 'Коммерческое предложение';
                        break;
                    case "costing":
                        $tpl = $path . 'costing_print.xlsx';
                        $last_cord = 'L';
                        $list_row = 34;
                        $file_title = 'Калькуляция';
                        break;
                    case "costing_purchase":
                        $tpl = $path . 'costing_purchase_print.xlsx';
                        $last_cord = 'L';
                        $list_row = 34;
                        $file_title = 'Калькуляция';
                        break;
                    case "ks2":
                        $tpl = $path . 'ks_print.xlsx';
                        $last_cord = 'L';
                        $list_row = 34;
                        $file_title = 'КС-2';
                        break;
                }
                $reader = IOFactory::createReader("Xlsx");
                $spreadsheet = $reader->load($tpl);
                $oCells = $spreadsheet->getActiveSheet()->getCellCollection();
                $tag_array = array();
                $tag_array_list = array();
                for ($iRow = 1; $iRow <= $oCells->getHighestRow(); $iRow++) {
                    for ($iCol = 'A'; $iCol <= 'Z'; $iCol++) {
                        $oCell = $oCells->get($iCol . $iRow);
                        if ($oCell) {
                            $st = preg_match("#\\$\\{(.*?)\\}#is", $oCell->getValue(), $matches);
                            if ($st) {
                                $tag_array[] = array('tag' => $matches[1], 'cord' => $iCol . $iRow);
                            }
                            $st_list = preg_match("#\\$\\[(.*?)\\]#is", $oCell->getValue(), $matches);
                            if ($st_list) {
                                $tag_array_list[] = array('tag' => $matches[1], 'cord' => $iCol);
                                $list_row = $iRow;
                            }
                        }
                    }
                }

                foreach ($tag_array as $key => $value) {
                    if (($value['tag'] != 'logo') && ($value['tag'] != 'sign')) {
                        $spreadsheet->setActiveSheetIndex(0)->setCellValue($value['cord'], (isset($tag_array_value[$value['tag']]) ? $tag_array_value[$value['tag']] : ''));
                    } else {
                        if ($value['tag'] == 'logo') {
                            $sheeti = new Drawing();
                            $sheeti->setName($value['tag']);
                            $sheeti->setDescription($value['tag']);
                            $sheeti->setPath($tag_array_value[$value['tag']]);
                            $sheeti->setHeight(56);
                            $sheeti->setCoordinates($value['cord']);
                            $sheeti->setWorksheet($spreadsheet->getActiveSheet());
                            $spreadsheet->setActiveSheetIndex(0)->setCellValue($value['cord'], '');
                        }
                        if (($type == 'bill_sign') && ($value['tag'] == 'sign')) {
                            $sheeti = new Drawing();
                            $sheeti->setName($value['tag']);
                            $sheeti->setDescription($value['tag']);
                            $sheeti->setPath($tag_array_value[$value['tag']]);
                            $sheeti->setHeight(228);
                            $sheeti->setCoordinates($value['cord']);
                            $sheeti->setWorksheet($spreadsheet->getActiveSheet());
                            $spreadsheet->setActiveSheetIndex(0)->setCellValue($value['cord'], '');
                        }
                    }
                }
                $row = $list_row;
                $count_list = 0;
                foreach ($order_data as $key => $value) {
                    $count_list++;
                    $s_cord = '';
                    foreach ($tag_array_list as $k => $v) {
                        if (isset($value[$v['tag']])) {

                            $spreadsheet->setActiveSheetIndex(0)->setCellValue($v['cord'] . $row, $value[$v['tag']]);
                            if (!empty($s_cord)) {
                                for ($ii = 'A'; $ii <= 'Z'; $ii++) {
                                    $iii = $ii;
                                    if (++$iii == $v['cord']) {
                                        $e_cord = $ii;
                                    }
                                }
                                $spreadsheet->getActiveSheet()->mergeCells($s_cord . $row . ':' . $e_cord . $row);
                            }
                            $s_cord = $v['cord'];
                            if (($v['tag'] == 'time')) {
                                $e_cord = $last_cord;
                                $spreadsheet->getActiveSheet()->mergeCells($s_cord . $row . ':' . $e_cord . $row);
                            }
                            if (($v['tag'] == 'total') && ($type == 'ks2')) {
                                $e_cord = $last_cord;
                                $spreadsheet->getActiveSheet()->mergeCells($s_cord . $row . ':' . $e_cord . $row);
                            }

                            if (($v['tag'] == 'name') && (mb_strlen($value['name']) >= 54)) {
                                $lenght = mb_strlen($value['name']);
                                $height = 11.25 * (floor($lenght / 53) + 1);
                                $spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight($height);
                            }
                        }
                    }
                    $row++;

                    if ($count != $count_list) {
                        $spreadsheet->getActiveSheet()->insertNewRowBefore($row, 1);
                    }
                }


                $oWriter = IOFactory::createWriter($spreadsheet, 'Xlsx');

                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="' . $file_title . ' ' . $tag_array_value['doc_full'] . '.xlsx"'); /*-- $filename is  xsl filename ---*/
                header('Cache-Control: max-age=0');
                $oWriter->save('php://output');
                exit;
            } else {
                $sign = false;
                switch ($type) {
                    default:
                        $tpl = $path . 'sales_print.docx';
                        break;
                    case "sales":
                        $tpl = $path . 'sales_print.docx';
                        break;
                    case "bill":
                        $tpl = $path . 'bill_print.docx';
                        break;
                    case "bill_sign":
                        if (is_file($path . 'bill_print_sign_' . $org_id . '.docx')) {
                            $tpl = $path . 'bill_print_sign_' . $org_id . '.docx';
                        } else {
                            $tpl = $path . 'bill_print_sign.docx';
                        }
                        $sign = true;
                        break;
                    case "kp":
                        $tpl = $path . 'kp_print.docx';
                        break;
                    case "kp_sign":
                        $tpl = $path . 'kp_print_sign.docx';
                        $sign = true;
                        break;
                    case "kp_locos":
                        $tpl = $path . 'kp_locos.docx';
                        break;
                }
                // \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(false);
                $document =  new \Boot\System\Office\Word\TemplateProcessor($tpl);

                $oTextRun = new \PhpOffice\PhpWord\Element\TextRun();
                $oLink = $oTextRun->addLink('http://www.google.com/', 'Hyperlink');
                $document->addLink($oLink);
                // $document->setComplexValue('doc_numb', $oLink); 

                $document->setValue('doc_numb', $tag_array_value['doc_numb']);   //$numb               
                $document->setValue('object_name', $tag_array_value['object_name']);   //$numb               
                $document->setValue('count', count($order_data));
                $document->setValue('day', $tag_array_value['day']);
                $document->setValue('month', $tag_array_value['month']);
                $document->setValue('year', $tag_array_value['year']);
                $document->setValue('oid_name', $tag_array_value['oid_name']);
                $document->setValue('oid_inn', $tag_array_value['oid_inn']);
                $document->setValue('oid_address', $tag_array_value['oid_address']);
                $document->setValue('org_name', $tag_array_value['org_name']);
                $document->setValue('org_inn', $tag_array_value['org_inn']);
                $document->setValue('org_address', $tag_array_value['org_address']);
                $document->setValue('org_bank_address', $tag_array_value['org_bank_address']);
                $document->setValue('org_bank_name', $tag_array_value['org_bank_name']);
                $document->setValue('org_bank_bik', $tag_array_value['org_bank_bik']);
                $document->setValue('org_bank_ks', $tag_array_value['org_bank_ks']);
                $document->setValue('org_bank_rs', $tag_array_value['org_bank_rs']);
                $document->setValue('org_nds_total_view', $org_nds_total_view);

                if (isset($org["org"]["change_alert"]) && $org["org"]["change_alert"] == 1) {
                    $document->setValue('change_alert', 'Внимание! Изменились реквизиты расчетного счета!');
                } else {
                    $document->setValue('change_alert', '');
                }

                if (file_exists($tag_array_value['logo'])) {
                    $document->setImageValue('logo',  array('path' => $tag_array_value['logo'], 'width' => '76mm', 'height' => '16mm'));
                } else {
                    $document->setValue('logo', '');
                }
                if ($org_id == 1 && !empty($tag_array_value['promotions_url']) && file_exists($tag_array_value['sale'])) {
                    $document->setImageValue('sale',  array('path' => $tag_array_value['sale'], 'link' => 'https://atri-energo.ru' . $tag_array_value['promotions_url'], 'width' => '114mm', 'height' => '35mm'));
                } else {
                    $document->setValue('sale', '');
                }
                if ($sign && file_exists($tag_array_value['sign'])) {
                    $document->setImageValue('sign',  array('path' => $tag_array_value['sign'], 'width' => '190mm', 'height' => '54mm'));
                }

                // dd($order_data_with_link);
                if ($org_id == 1) {
                    $document->cloneRowAndSetValues('n', $order_data_with_link ?? $order_data);
                } else {
                    $document->cloneRowAndSetValues('n', $order_data);
                }


                $document->setValue('nds_includes', $tag_array_value['nds_includes']);
                $document->setValue('total_with_nds', $tag_array_value['total_with_nds']);
                $document->setValue('total_with_nds_sum', $total_with_nds_sum);
                $document->setValue('twndssum', $total_with_nds_sum);

                $document->setValue('oid_kpp', $tag_array_value['oid_kpp']);

                $document->setValue('org_kpp', $tag_array_value['org_kpp']);
                $document->setValue('org_kpp_only', $tag_array_value['org_kpp_only']);

                $document->setValue('org_director', $tag_array_value['org_director']);
                $document->setValue('org_accountant', $tag_array_value['org_accountant']);
                $document->setValue('manager', $tag_array_value['manager_name']);
                $document->setValue('manager_email', $tag_array_value['manager_email']);
                $document->setValue('manager_phone', $tag_array_value['manager_phone']);

                $document->setValue('total', $total_with_nds_sum);

                if (!empty($nds_down)) {
                    $nds_down .= " " . $tag_array_value['nds'] . " руб.";
                }
                $document->setValue('nds_view', $nds_down);
                $document->setValue('nds', $tag_array_value['nds']);
                $document->setValue('total_nds', $tag_array_value['total']);

                $document->setValue('total_rus', $tag_array_value['total_rus']);

                $order_date = date("d-m-Y", strtotime($request["date"]));
                $file_name = $type . '_' . $request["doc_numb"] . '_' . $order_date;
                // dd($file_name, true);
                $file_url = ROOT . 'files/' . $module . '/' . $id . '/' . $file_name . '.docx';
                $file_url_pdf = ROOT . 'files/' . $module . '/' . $id . '/' . $file_name . '.pdf';
                $document->saveAs($file_url);

                if (isset($request['format']) && $request['format'] == 'pdf') {
                    $file_url_output = $file_url_pdf;
                    $file_url_output = str_replace(ROOT, "/", $file_url_output);
                } else {
                    $file_url_output = $file_url;
                    $file_url_output = str_replace(ROOT, "/", $file_url_output);
                }
            }
        }

        if (isset($request['autoprint']) && $request['autoprint'] == 'true' && (isset($request['format']) && $request['format'] == 'pdf')) {

            // \Boot\Jobs\PdfJob::dispatchSync($file_url, $file_name.".pdf");
            $pdfJob =  new \Boot\Jobs\PdfJob($file_url, $file_name . ".pdf");
            $pdfJob->handle($file_url, $file_name . ".pdf");
            $pdf = new PDF_AutoPrint();
            $pageCount = $pdf->setSourceFile($file_url_pdf);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tplIdx = $pdf->importPage($pageNo);
                $pdf->addPage();
                $pdf->useTemplate($tplIdx, null, null, null, null, true);
            }
            $pdf->AutoPrint(true);
            $pdf->Output($file_name . ".pdf", "I");
            unlink($file_url);
            unlink($file_url_pdf);
        } else {
            if (isset($request['format']) && $request['format'] == 'pdf') {
                $pdfJob =  new \Boot\Jobs\PdfJob($file_url, $file_name . ".pdf");
                $pdfJob->handle($file_url, $file_name . ".pdf");
                
                OfficeSupport::get_file($file_url_pdf);
                unlink($file_url);
                exit;
            }

            header('Location: ' . $file_url_output);
        }
    }
}

class PDF_JavaScript extends \setasign\Fpdi\Fpdi
{

    var $javascript;
    var $n_js;

    function IncludeJS($script)
    {
        $this->javascript = $script;
    }

    function _putjavascript()
    {
        $this->_newobj();
        $this->n_js = $this->n;
        $this->_out('<<');
        $this->_out('/Names [(EmbeddedJS) ' . ($this->n + 1) . ' 0 R]');
        $this->_out('>>');
        $this->_out('endobj');
        $this->_newobj();
        $this->_out('<<');
        $this->_out('/S /JavaScript');
        $this->_out('/JS ' . $this->_textstring($this->javascript));
        $this->_out('>>');
        $this->_out('endobj');
    }

    function _putresources()
    {
        parent::_putresources();
        if (!empty($this->javascript)) {
            $this->_putjavascript();
        }
    }

    function _putcatalog()
    {
        parent::_putcatalog();
        if (!empty($this->javascript)) {
            $this->_out('/Names <</JavaScript ' . ($this->n_js) . ' 0 R>>');
        }
    }
}

class PDF_AutoPrint extends PDF_JavaScript
{
    function AutoPrint($dialog = false)
    {
        //Open the print dialog or start printing immediately on the standard printer
        $param = ($dialog ? 'true' : 'false');
        $script = "print($param);";
        $this->IncludeJS($script);
    }

    function AutoPrintToPrinter($server, $printer, $dialog = false)
    {
        $script = "document.contentWindow.print();";
        $this->IncludeJS($script);
    }
}
