<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Decrees\Tasks;

use Decrees\Models\Decrees;
use Boot\Abstracts\Tasks\Task;
use Boot\System\Office\Word\TemplateProcessor;

class DocCreateTask extends Task
{
    /**
     * Генерация номера приказа в соответствии организации типу и счетчику
     *
     * @param  Decrees $decree
     * @return Decrees
     */
    public function run(Decrees $decree, $type)
    {
        $org = getOrgInfo($decree->org, 'object');
        $document =  new TemplateProcessor(modules_path('decrees/Resources/Templates/' . $type . '.docx'));
        $document->setValue('org_name', $org->organizations->name_full);
        $document->setValue('org_name_full', $org->organizations->name_full);
        $document->setValue('org_yaddress', $org->organizations->yaddress);
        $document->setValue('org_maddress', $org->organizations->maddress);
        $document->setValue('org_inn', $org->organizations->inn);
        $document->setValue('org_kpp', $org->organizations->kpp);
        $document->setValue('org_ogrn', $org->organizations->ogrn);
        $document->setValue('org_post', $org->organizations->post);
        $document->setValue('org_director', $org->organizations->director);
        $document->setValue('org_phone', $org->organizations->phone);
        $document->setValue('org_email', $org->organizations->email);
        $document->setValue('org_bank_bik', $org->organizations_bank->bik);
        $document->setValue('org_bank_name', $org->organizations_bank->name);
        $document->setValue('org_bank_rschet', $org->organizations_bank->rschet);
        $document->setValue('org_bank_kschet', $org->organizations_bank->kschet);
        switch ($type) {
            default:
            case 'decree':
                $document->setValue('doc_numb', $decree->numb_counter);
                $document->setValue('doc_text', $decree->text ?? '');
                break;
            case 'job':
            case 'trip':
                $target = isset($data["target"]) ? $data["target"] : "";
                if ($type == 'trip') {
                    $destination_once = "";
                    $destination = [
                        "data" => []
                    ];
                    if (isset($data["destination"])) {
                        $flag_i = true;
                        $count = count($data["destination"]);
                        foreach ($data["destination"] as $val) {
                            if ($count > 1) {
                                $zap = ",";
                            } else {
                                $zap = "";
                            }
                            $org = empty($val["org"]) ? " " : " {$val["org"]}";
                            if ($flag_i) {
                                $destination_once = "г. " . $val["city"] . $org . $zap;
                                $flag_i = false;
                            } else {
                                $destination["data"][] = "г. " . $val["city"] . $org . $zap;
                            }
                            $count--;
                        }
                    } else {                       
                        $destination_once = "";
                        $destination = [
                            "city" => [],
                            "org" => [],
                        ];
                        $short_report = [
                            "data" => [],
                            "target" => [],
                        ];
                        $flag_once = true;
                        if (isset($data["destination"])) {
                            foreach ($data["destination"] as $val) {
                                $destination["city"][] = "г. " . $val["city"];
                                $destination["org"][] = $val["org"] ?? "";
                                $short_report["data"][] = empty($val["short_report"]) ? "" : $val["short_report"];
                                $short_report["target"][] = $flag_once ? $target : "";
                                $flag_once = false;
                            }
                        }  
                        $document->setValue('ended_at', $org->organizations->name_full);
                        
                        
                        $date_transit = isset($data["date_transit"]) ? $data["date_transit"] : "";

                        $document->setValue('date_transit', $date_transit);
                        $document->setValue('date_end', date("d.m.Y", strtotime($order["date_end"])));
                        // $document->cloneRow('short_report', $short_report);
                    }
                }
                $document->setValue('org_name', $org->organizations->name_full);
                $document->setValue('{date}', date("d.m.Y", strtotime($order["date"])));
                $full_name_uid = json_decode($order["worker"], true);
                $document->setValue('{full_name}', fullUserName($full_name_uid[0]));
                $date_start = new DateTime($order["date"]);
                $date_end = new DateTime($order["date_end"]);
                $document->setValue('{date_day}', $date_start->format("d"));
                $document->setValue('{end_date_day}', $date_end->format("d"));
                $month =  ['Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'];
                $document->setValue('{date_month}', $month[$date_start->format("n") - 1]);
                $document->setValue('{end_date_month}', $month[$date_end->format("n") - 1]);
                $document->setValue('{date_year}', $date_start->format("y"));
                $document->setValue('{end_date_year}', $date_end->format("y"));
                $interval = $date_start->diff($date_end, true)->days + 1;
                $document->setValue('{term}', $interval);
                $document->setValue('{numb}', $order["numb"]);
                $director_short_name_query = $db->query("SELECT uid FROM " . DB_PREFIX . "_users_org WHERE type = '1' AND oid = '" . $order["o_id"] . "' LIMIT 1");
                if ($db->numRows($director_short_name_query) > 0) {
                    $q = $db->getRow($director_short_name_query);
                    $director_short_name = shortUserName($q["uid"]);
                } else {
                    $director_short_name = "";
                }
                $document->setValue('{director_short_name}', $director_short_name);
                $document->setValue('{source_financing}', $order["o_name"]);
                $document->setValue('{destination_once}', $destination_once);
                $document->cloneRow('destination', $destination);
                $post = isset($data["post"]) ? $data["post"] : "";
                $document->setValue('{position}', $post);
                $document->setValue('{target}', $target);
                break;
        }
        $file_url = ROOT . 'files/' . $decree->id . '-' . time() . '.docx';
        $document->saveAs($file_url);
        dd($decree);

        // $query_static = $db->query("SELECT com.*, o.name as o_name, o.id as o_id FROM " . DB_PREFIX . "_command as com LEFT JOIN " . DB_PREFIX . "_org as org ON(com.org = org.id) LEFT JOIN " . DB_PREFIX . "_organizations as o ON(o.id = org.oid) WHERE com.id = '" . $id . "'");
        // $order = $db->getRow($query_static);

        // require_once(ROOT . 'usr/plugins/PHPWord.php');
        // $PHPWord = new PHPWord();

        // switch ((int)$order["type"]) {
        //     default:
        //         $document = $PHPWord->loadTemplate(ROOT . '/files/tpl/order_doc_1.docx');
        //         $document->setValue('{doc_numb}', $order["numb_counter"]);
        //         break;
        //     case 3:
        //         $data = json_decode($order["data"], true);

        //        
        //         switch ($type) {
        //             default:
        //             case 1:
        //                 //приказ на командировку
        //                 $document = $PHPWord->loadTemplate(ROOT . '/files/tpl/command/command.docx');

        //                 $destination_once = "";
        //                 $destination = [
        //                     "data" => []
        //                 ];
        //                 if (isset($data["destination"])) {
        //                     $flag_i = true;
        //                     $count = count($data["destination"]);
        //                     foreach ($data["destination"] as $val) {
        //                         if ($count > 1) {
        //                             $zap = ",";
        //                         } else {
        //                             $zap = "";
        //                         }
        //                         $org = empty($val["org"]) ? " " : " {$val["org"]}";
        //                         if ($flag_i) {
        //                             $destination_once = "г. " . $val["city"] . $org . $zap;
        //                             $flag_i = false;
        //                         } else {
        //                             $destination["data"][] = "г. " . $val["city"] . $org . $zap;
        //                         }
        //                         $count--;
        //                     }
        //                 }
        //                 break;
        //             case 2:
        //                 //служебное задание на командировку
        //                 $document = $PHPWord->loadTemplate(ROOT . '/files/tpl/command/job_assignment.docx');

        //                 $document->setValue('{date_end}', date("d.m.Y", strtotime($order["date_end"])));

        //                 $destination_once = "";
        //                 $destination = [
        //                     "city" => [],
        //                     "org" => [],
        //                 ];
        //                 $short_report = [
        //                     "data" => [],
        //                     "target" => [],
        //                 ];
        //                 $flag_once = true;
        //                 if (isset($data["destination"])) {
        //                     foreach ($data["destination"] as $val) {
        //                         $destination["city"][] = "г. " . $val["city"];
        //                         $destination["org"][] = $val["org"] ?? "";
        //                         $short_report["data"][] = empty($val["short_report"]) ? "" : $val["short_report"];
        //                         $short_report["target"][] = $flag_once ? $target : "";
        //                         $flag_once = false;
        //                     }
        //                 }
        //                 $document->cloneRow('short_report', $short_report);
        //                 $date_transit = isset($data["date_transit"]) ? $data["date_transit"] : "";
        //                 $document->setValue('{date_transit}', $date_transit);
        //                 break;
        //         }

        //         $document->setValue('{org-name}', $order["o_name"]);

        //         $document->setValue('{date}', date("d.m.Y", strtotime($order["date"])));

        //         $full_name_uid = json_decode($order["worker"], true);
        //         $document->setValue('{full_name}', fullUserName($full_name_uid[0]));

        //         $date_start = new DateTime($order["date"]);
        //         $date_end = new DateTime($order["date_end"]);
        //         $document->setValue('{date_day}', $date_start->format("d"));
        //         $document->setValue('{end_date_day}', $date_end->format("d"));

        //         $month =  ['Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'];
        //         $document->setValue('{date_month}', $month[$date_start->format("n") - 1]);
        //         $document->setValue('{end_date_month}', $month[$date_end->format("n") - 1]);

        //         $document->setValue('{date_year}', $date_start->format("y"));
        //         $document->setValue('{end_date_year}', $date_end->format("y"));
        //         $interval = $date_start->diff($date_end, true)->days + 1;
        //         $document->setValue('{term}', $interval);

        //         $document->setValue('{numb}', $order["numb"]);

        //         $director_short_name_query = $db->query("SELECT uid FROM " . DB_PREFIX . "_users_org WHERE type = '1' AND oid = '" . $order["o_id"] . "' LIMIT 1");
        //         if ($db->numRows($director_short_name_query) > 0) {
        //             $q = $db->getRow($director_short_name_query);
        //             $director_short_name = shortUserName($q["uid"]);
        //         } else {
        //             $director_short_name = "";
        //         }
        //         $document->setValue('{director_short_name}', $director_short_name);

        //         $document->setValue('{source_financing}', $order["o_name"]);

        //         $document->setValue('{destination_once}', $destination_once);
        //         $document->cloneRow('destination', $destination);

        //         $post = isset($data["post"]) ? $data["post"] : "";
        //         $document->setValue('{position}', $post);

        //         $document->setValue('{target}', $target);
        //         break;
        // }
        // $file_url = 'files/command/' . $id . '-' . time() . '.docx';
        // $document->save(ROOT . $file_url);
    }
}
