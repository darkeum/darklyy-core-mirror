<?php

namespace Sales\Support;

class SalesReestrSupport{

    static function check($sales_id){
        $sales = \Sales\Model\Sales::findOrFail($sales_id);
        if(empty($sales->object)){
            throw new \Error("Не указан объект");
        }
        if(empty($sales->org)){
            throw new \Error("Не указана организация");
        }
        if(empty($sales->oid)){
            throw new \Error("Не указан контрагент");
        }
        $organization = \Organizations\Model\Organizations::findOrFail($sales->oid);
        if(empty($organization->inn)){
            throw new \Error("Не указан ИНН");
        }
        if(empty($organization->dey)){
            throw new \Error("Не указано основание контрагента");
        }
        if($organization->banks()->count() == 0){
            throw new \Error("Не указаны банковские данные контрагента");
        }
        return true;
    }

    static function contract_create($request){
        require_once(ROOT.'usr/plugins/PHPWord.php');
        //["sales_id" => "", "reestr_id" => "", "date_doc" => ""]
        $date = empty($request["date_doc"]) ? date("Y-m-d") : $request["date_doc"];

        $order = \Sales\Model\Sales::findOrFail($request["sales_id"])->toArray();

        parseCertById($order["oid"]);


        $org = \Organizations\Model\Organizations::findOrFail($order['oid'])->toArray();

        $bank = \Organizations\Model\OrganizationsBank::where("oid", $order['oid']);
        if(!empty($org['organizations_bank_default'])){
            $bank = $bank->where('id', $org['organizations_bank_default']);
        }

        $bank = $bank->firstOrFail()->toArray();

        if(!empty($bank) && isset($org['inn'], $org['dey'])){
            $reestr = \Reestr\Models\Reestr::findOrFail($request["reestr_id"])->toArray();
            $numb = $reestr["numb"];

            $path = ROOT."usr/modules/sales/resources/templates/";
            $PHPWord = new \PHPWord();
            switch((int)$order["org"]){
                default:
                    $document = $PHPWord->loadTemplate($path.'sales_doc_2.docx');
                break;
                case 1:
                    $document = $PHPWord->loadTemplate($path.'sales_doc_1.docx');
                break;
                case 5:
                    $document = $PHPWord->loadTemplate($path.'sales_doc_5.docx');
                break;
                case 11:
                    $document = $PHPWord->loadTemplate($path.'sales_doc_11.docx');
                break;
            }

            $org_main = getOrgInfo($order["org"]);

            $Month_rus = getMonthRus();

            switch($org['post']) {
                default:
                    $post=$org['post'];
                break;
                case "Директор":
                    $post='директора';
                break;
                case "Генеральный директор":
                    $post='генерального директора';
                break;
            }

            $dey = $org['dey'];

            $result_d = getDirectorDetail($org, $order);
            // if(\Boot\Support\Facades\Auth::id() == 33){
            // 	dd($result_d);
            // }

            $shortDirectorName = function($result_d){
                $result_s = mb_substr($result_d->name, 0, 1) . '. ';
                $result_s .= mb_substr($result_d->patronymic, 0, 1) . '. ';
                $result_s .=  $result_d->surname;
                return $result_s;
            };

            $result_s = $shortDirectorName($result_d);

            $document->setValue('{doc_numb}', $numb); //doc, spec
            $document->setValue('{post}', $post); //doc

            if($org['type'] == "INDIVIDUAL"){
                $document->setValue('{post_i}', "Индивидуальный предприниматель"); //doc, spec
            } else {
                $document->setValue('{post_i}', $org['post']); //doc, spec
            }

            $document->setValue('{director}', $result_d->result_genitive); //doc
            $document->setValue('{director_short}', $result_s); //doc, spec
            $document->setValue('{dey}', $dey); //doc

            $document->setValue('{day}', date('d', strtotime($date))); //doc, spec разные
            $document->setValue('{month}', $Month_rus[ date( "n", strtotime($date)) - 1 ]); //doc, spec разные
            $document->setValue('{year}', date('Y', strtotime($date))); //doc, spec разные

            $document->setValue('{org_name}', $org['name']); //doc, spec
            $document->setValue('{org_inn}', $org['inn']); //doc
            $document->setValue('{org_address}', $org['yaddress']); //doc
            if($org['type'] == "INDIVIDUAL"){
                $document->setValue('{org_kpp}', ""); //doc
            } else {
                $document->setValue('{org_kpp}', "КПП: ".$org['kpp']); //doc
            }

            $document->setValue('{org_bank}', $bank['name_short']); //doc
            $document->setValue('{org_bik}', $bank['bik']); //doc
            $document->setValue('{org_rs}', $bank['rschet']); //doc
            $document->setValue('{org_ks}', $bank['kschet']); //doc

            //main org
            $document->setValue('{main_org_name}', $org_main["organizations"]['name']);
            $document->setValue('{main_org_inn}', $org_main["organizations"]['inn']);
            $document->setValue('{main_org_kpp}', $org_main["organizations"]['kpp']);
            $document->setValue('{main_org_address}', $org_main["organizations"]['yaddress']);

            $document->setValue('{main_org_bank}', $org_main['organizations_bank']['name_short']);
            $document->setValue('{main_org_bik}', $org_main['organizations_bank']['bik']);
            $document->setValue('{main_org_rs}', $org_main['organizations_bank']['rschet']);
            $document->setValue('{main_org_ks}', $org_main['organizations_bank']['kschet']);

            $document->setValue('{main_director_short}',  $shortDirectorName($org_main["organizations"]["director_detail"]));

            return $document;
        }
    }

    static function spec_create($request){
        require_once(ROOT.'usr/plugins/PHPWord.php');
        //["pr" => "", "sales_id" => "", "reestr_id" => "", "date_doc" => "", "reestr_doc_id" => ""]
        $date = empty($request["date_doc"]) ? date("Y-m-d") : $request["date_doc"];

        $reestr = \Reestr\Models\Reestr::findOrFail($request["reestr_id"])->toArray();
        $reestr_doc = \Reestr\Models\ReestrDoc::findOrFail($request["reestr_doc_id"])->toArray();
        $doc_date = date("Y-m-d", strtotime($reestr["date_view"]));

        $order = \Sales\Model\Sales::findOrFail($request["sales_id"])->toArray();
        $org = \Organizations\Model\Organizations::findOrFail($order['oid'])->toArray();
        $bank = \Organizations\Model\OrganizationsBank::where("oid", $order['oid']);
        if(!empty($org['organizations_bank_default'])){
            $bank = $bank->where('id', $org['organizations_bank_default']);
        }

        $bank = $bank->firstOrFail()->toArray();



        $pr = empty($request['pr']) ? 0 : $request['pr'];

        $delivery = empty($request['delivery']) ? 1 : $request['delivery'];

        $deferment = empty($request['deferment']) ? 30 : $request['deferment'];

        parseCertById($order['oid']);

        if(!empty($bank) && isset($org['inn'], $org['dey'])){
            $address = empty($request['address']) ? $org["yaddress"] : $request['address'];

            $numb = $reestr["numb"];

            $path = ROOT."usr/modules/sales/resources/templates/";
            $PHPWord = new \PHPWord();
            switch((int)$order["org"]){
                default:
                    $document = $PHPWord->loadTemplate($path.'sales_spec_2.docx');
                break;
                case 1:
                    $document = $PHPWord->loadTemplate($path.'sales_spec_1.docx');
                break;
                case 5:
                    $document = $PHPWord->loadTemplate($path.'sales_spec_5.docx');
                break;
                case 11:
                    $document = $PHPWord->loadTemplate($path.'sales_spec_11.docx');
                break;
            }

            $Month_rus = getMonthRus();

            $result_d = getDirectorDetail($org, $order);

            $order_data = [
                    'n' => [],
                    'sku' => [],
                    'name' => [],
                    's' => [],
                    'u' => [],
                    'price' => [],
                    'price_nds' => [],
                    'date_post' => [],
            ];

            $i = 0;
            foreach(\Sales\Model\SalesDetail::where("sales_id", $request["sales_id"])->orderBy("position")->get()->toArray() as $catalog) {
                $i++;
                $stable_name = $catalog['s_name'];
                $stable_sku = $catalog['s_sku'];
                $stable_unit = $catalog['s_unit'];
                $sum = $catalog['count'];
                $price = round(($catalog['total']/ $catalog['count']), 2);
                $total_nds = $catalog['total'];
                array_push($order_data['n'], $i);
                array_push($order_data['sku'], $stable_sku);
                array_push($order_data['name'], $stable_name);
                array_push($order_data['s'], $sum);
                array_push($order_data['u'],  get_unit('data', $stable_unit));
                array_push($order_data['price'], number_format($price,2,'.',' '));
                array_push($order_data['price_nds'], number_format($total_nds,2,'.',' '));
                $gppds = \Sales\Model\SalesDetail::getDatePost($catalog["id"]);
                $date_post = empty($gppds->date_post) ? '' : $gppds->date_post; //Уточняется
                array_push($order_data['date_post'], $date_post);
            }

            $result_s = mb_substr($result_d->name, 0, 1) . '. ';
            $result_s .= mb_substr($result_d->patronymic, 0, 1) . '. ';
            $result_s .=  $result_d->surname;

            $total = number_format($order['total'],2,'.',' ');
            $total_rus = '('.mb_ucfirst(num2str($order['total']));

            if ($pr <= 0){
                $pay = 'Покупатель вносит сумму в размере 100% '.$total.' '.$total_rus.' в течение '.$deferment.' рабочих дней со дня отгрузки.';
            } elseif(($pr > 0)&&($pr < 100)){
                $pr_o = 100 - $pr;
                $total_pr = $pr / 100 * $order['total'];
                $total_r = number_format($total_pr,2,'.',' ');
                $total_r_rus = '('.mb_ucfirst(num2str($total_pr));
                $total_pr_o = $pr_o / 100 * $order['total'];
                $total_r_o = number_format($total_pr_o,2,'.',' ');
                $total_r_o_rus = '('.mb_ucfirst(num2str($total_pr_o));
                $pay = 'Покупатель вносит предоплату в размере '.$pr.'%: '.$total_r.' '.$total_r_rus.' в течение 3 (Трех) рабочих дней с момента подписания настоящей спецификации, оставшуюся сумму в размере '.$pr_o.'% '.$total_r_o.' '.$total_r_o_rus.' в течение '.$deferment.' календарных дней со дня отгрузки.';
            } else {
                $pay = 'Покупатель вносит предоплату в размере 100% '.$total.' '.$total_rus.' в течение 3 (Трёх) рабочих дней с момента подписания настоящей спецификации.';
            }

            $delivery_text = "";
            switch((int)$delivery){
                case 1:
                    $delivery_text = "самовывоз со склада Продавца по адресу: УР, г. Ижевск, ул. Салютовская, 5а.";
                break;
                case 2:
                    $delivery_text = "доставка на склада Покупателя по адресу: ".$address.". Стоимость доставки входит в стоимость товара.";
                break;
            }
            $document->setValue('{delivery}', $delivery_text);


            $document->setValue('{doc_numb}', $numb); //doc, spec
            $spec_numb = $reestr_doc["spec_numb"];
            $document->setValue('{spec_numb}', $spec_numb); //doc, spec

            if($org['type'] == "INDIVIDUAL"){
                $document->setValue('{post_i}', "Индивидуальный предприниматель"); //doc, spec
            } else {
                $document->setValue('{post_i}', $org['post']); //doc, spec
            }
            $document->setValue('{director_short}', $result_s); //doc, spec
            $document->setValue('{pay}', $pay); //spec

            $document->setValue('{doc_day}', date('d', strtotime($doc_date))); //doc, spec разные
            $document->setValue('{doc_month}', $Month_rus[ date( "n", strtotime($doc_date)) - 1 ]); //doc, spec разные
            $document->setValue('{doc_year}', date('Y', strtotime($doc_date))); //doc, spec разные

            $document->setValue('{day}', date('d', strtotime($date))); //doc, spec разные
            $document->setValue('{month}', $Month_rus[ date( "n", strtotime($date)) - 1 ]); //doc, spec разные
            $document->setValue('{year}', date('Y', strtotime($date))); //doc, spec разные

            $document->setValue('{org_name}', $org['name']); //doc, spec

            $document->cloneRow('s', $order_data); //spec
            $document->setValue('{total}', $total); //spec
            $document->setValue('{nds}',  number_format($order['nds'],2,'.',' ')); //spec
            $document->setValue('{total_nds}', number_format($order['total'],2,'.',' ')); //spec
            $document->setValue('{total_rus}', $total_rus ); //spec

            // $dir = ROOT.'files/sales/'.$id;
            // if (!file_exists($dir)){
            // 	mkdir($dir, 0777);
            // }
            // $file_url= 'files/sales/'.$id.'/8-'.$numb.'-'.time().'.docx';
            // $document->save($file_url);
            return $document;
        }
    }

    static function save_file_attach($request, $document, $prefix){
        $dir = ROOT.'files/sales/'.$request["sales_id"];
        if (!file_exists($dir)){
            mkdir($dir, 0777, true);
        }
        $name = $prefix.'_'.time().'.docx';
        $file_url= 'files/sales/'.$request["sales_id"].'/'.$name;
        $document->save(ROOT.$file_url);
        return attach_insert_file(["name" => $name, "url" => $file_url, "pub_id" => $request["reestr_id"], "mod" => "reestr",]);
    }

    static function get_contract($request){
        $document = static::contract_create($request);
        return static::save_file_attach($request, $document, "contract");
    }

    static function get_spec($request){
        $document = static::spec_create($request);
        return static::save_file_attach($request, $document, "spec");
    }
}