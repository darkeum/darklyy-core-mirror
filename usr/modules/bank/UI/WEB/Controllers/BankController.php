<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Bank\UI\WEB\Controllers;

use Carbon\Carbon;
use Sales\Models\Sales;
use Bank\Models\BankPay;
use Bank\Models\BankDetail;
use Bank\Models\BankUpload;
use Supply\Models\Receipts;
use Boot\System\Http\Request;
use Supply\Models\SupplyOrder;
use Boot\Support\Facades\Auth;
use Boot\Controller\AbstractController;
use Darkeum\ClientBankExchange\Parser;
use Organizations\Model\OrganizationsBank;
use Organizations\Support\OrganizationsSupport;

class BankController extends AbstractController
{
    static function index()
    {
        return view('bank::manager.index');
    }

    static function create()
    {
        return view('bank::manager.create', [
            'select_sales' => static::getSelect([
                "name" => "select_sales",
                "ajaxUrl" => "getSalesToSelect",
                "select_class" => "sales_select",
            ]),
            'select_supply_order' => static::getSelect([
                "name" => "select_supply_order",
                "ajaxUrl" => "getSupplyOrderToSelect",
                "select_class" => "supply_order_select",
            ]),
        ]);
    }

    private static function getSelect($request)
    {
        $oSelectSettingJS = [
            "mod" => "jsReturn",
            "name" => $request["name"],
            "ajaxUrl" => $request["ajaxUrl"],
            "id" => $request["id"] ?? null,
            "return" => true,
            "bootstrap" => 'default',
        ];

        if (isset($request["select_class"])) {
            $oSelectSettingJS["select_class"] = $request["select_class"];
        }

        $oSelectSettingSelect = $oSelectSettingJS;
        $oSelectSettingSelect["mod"] = "select";
        return [
            "js" => oSelectCustom($oSelectSettingJS),
            "select" => oSelectCustom($oSelectSettingSelect),
        ];
    }

    static function parse(Request $request)
    {
        $file = static::validationFile($request);
        $p = new Parser($file->getPathName());

        $rschet = $p->filter->РасчСчет;
        $organizations_bank = OrganizationsBank::with("organization.org")->where("rschet", $rschet)->first();
        $documents = [];
        foreach ($p->documents as $item) {
            $destination_type = $item->ПлательщикИНН == $organizations_bank->organization->inn ? "supply_order" : "sales";
            $option = ["value" => "", "text" => "", "total" => $item->Сумма];
            $search_func = $destination_type . "_search";
            if (method_exists(static::class, $search_func)) {
                $data = static::$search_func($item);
                if ($data) {
                    $option["value"] = $data["value"];
                    $option["text"] = $data["text"];
                }
            }
            if ($p->general->ВерсияФормата == 1.01) {
                $documents[] = [
                    "type" => $item->type,
                    "destination_type" => $destination_type,
                    "payer" => $item->Плательщик1,
                    "recipient" => $item->Получатель1,
                    "purpose_payment" => $item->НазначениеПлатежа,
                    "total" => $item->Сумма,
                    "total_view" => number_format($item->Сумма, 2, ',', '&nbsp;'),
                    "payer_inn" => $item->ПлательщикИНН,
                    "select" => [$option],
                ];
            } else {
                $documents[] = [
                    "type" => $item->type,
                    "destination_type" => $destination_type,
                    "payer" => $item->Плательщик,
                    "recipient" => $item->Получатель,
                    "purpose_payment" => $item->НазначениеПлатежа,
                    "total" => $item->Сумма,
                    "total_view" => number_format($item->Сумма, 2, ',', '&nbsp;'),
                    "payer_inn" => $item->ПлательщикИНН,
                    "select" => [$option],
                ];
            }
        }

        return json_encode([
            "documents" => $documents,
            "organizations_bank" => $organizations_bank,
        ], JSON_UNESCAPED_UNICODE);

    }

    private static function supply_order_search($item)
    {
        $num = [];
        $data = SupplyOrder::whereHas("organization", function ($q) use ($item) {
            $q->where("inn", $item->ПолучательИНН);
        });

        preg_match("/№.?[а-яёА-ЯЁ]*-?[0-9]+/", $item->НазначениеПлатежа, $search_reg);
        if (isset($search_reg[0])) {
            $num_ = $search_reg[0];
            $num_ = str_replace("№", "", $num_);
            $num_ = trim($num_);
            $num[] = $num_;
        }
        preg_match("/N.?[а-яёА-ЯЁ]*-?[0-9]+\/?[0-9]+\/?[0-9]+/", $item->НазначениеПлатежа, $search_reg);
        if (isset($search_reg[0])) {
            $num_ = $search_reg[0];
            $num_ = trim($num_);
            $num_ = str_replace("N", "", $num_);
            $num[] = $num_;
            $num_ = str_replace("418/", "", $num_);
            $num_ = str_replace("/418", "", $num_);
            $num[] = $num_;
        }
        $text = "по счету";
        preg_match("/{$text}.?[0-9]+/", $item->НазначениеПлатежа, $search_reg);
        if (isset($search_reg[0])) {
            $num_ = $search_reg[0];
            $num_ = str_replace($text, "", $num_);
            $num_ = trim($num_);
            $num[] = $num_;
        }

        if ($num) {
            $data = $data->where(function ($query) use ($num) {
                foreach ($num as $n) {
                    $query->orWhere("bill_code", "LIKE", "{$n}%");
                }
            })->orderByDesc("id")->first();
            if (!empty($data)) {
                return ["value" => $data->id, "text" => "{$data->numb} {$data->organization->name} ({$data->bill_code})"];
            }
        }

        $receipts = static::receipts_search($item);
        if ($receipts) return $receipts;
    }

    private static function receipts_search($item)
    {
        $num = [];
        $data = Receipts::whereHas("organization", function ($q) use ($item) {
            $q->where("inn", $item->ПолучательИНН);
        })->with("supply_order");

        $text = "счет-фактуре";
        preg_match("/{$text}.?[0-9]+/", $item->НазначениеПлатежа, $search_reg);
        if (isset($search_reg[0])) {
            $num_ = $search_reg[0];
            $num_ = str_replace($text, "", $num_);
            $num_ = trim($num_);
            $num[] = $num_;
        }

        if ($num) {
            $data = $data->where(function ($query) use ($num) {
                foreach ($num as $n) {
                    $query->orWhere("doc_number", "LIKE", "{$n}%");
                }
            })->orderByDesc("id")->first();
            if (!empty($data)) {
                return ["value" => $data->supply_order->id, "text" => "{$data->supply_order->numb} {$data->organization->name} ({$data->supply_order->bill_code})"];
            }
        }
    }

    private static function b_translit($str)
    {
        $rus = array('А', 'В', 'Е', 'О', 'Р', 'С', 'Т', 'Х', 'Н',);
        $lat = array('A', 'B', 'E', 'O', 'P', 'C', 'T', 'X', 'H');
        return str_replace($rus, $lat, $str);
    }

    private static function sales_search($item)
    {
        $translate = static::b_translit($item->НазначениеПлатежа);
        preg_match("/[a-zA-Z]{1,2}PN.?-.?[0-9]+/", $translate, $search_reg);
        $num = $search_reg[0] ?? false;
        if ($num) {
            $num = str_replace(" ", "", $num);
            $num = str_replace("№", "", $num);
            $num = str_replace('b"', "", $num);
            $data = Sales::whereHas("organization", function ($q) use ($item) {
                $q->where("inn", $item->ПлательщикИНН);
            })->where("numb", "LIKE", "%{$num}")->orderByDesc("id")->first();
            if (!empty($data)) {
                return ["value" => $data->id, "text" => "{$data->numb} {$data->organization->name}"];
            }
        }
    }

    static function store(Request $request)
    {
        if ($request->has("data")) {
            $file = static::validationFile($request);
            $data = json_decode($request->get("data"));
            $patch = 'files/bank/';
            $upload = new \Boot\System\Upload\Upload($file->getPathName());
            $upload->file_overwrite = true;
            $upload->file_new_name_body = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $upload->process(ROOT . $patch);
            if ($upload->processed) {
                $upload->clean();
            }

            $p = new Parser($upload->file_dst_pathname);

            $BankUpload = BankUpload::create([
                "uid" => Auth::id(),
                "org" => $data->organizations_bank->organization->org->id,
                "date" => date("Y-m-d", strtotime($p->general->ДатаСоздания)) . " " . $p->general->ВремяСоздания,
                "date_add" => Carbon::now(),
                "balance_in" => $p->remainings->offsetExists('НачальныйОстаток') ? $p->remainings->НачальныйОстаток : null,
                "balance_out" => $p->remainings->offsetExists('КонечныйОстаток') ? $p->remainings->КонечныйОстаток : null,
                "file" => $patch . $upload->file_dst_name,
                "status" => 1,
            ]);

            $sales_a = [];
            foreach ($p->documents as $key => $d) {
                $item = $data->documents[$key];

                try {
                    $payer = OrganizationsSupport::createOrgByInn($d->ПлательщикИНН);
                } catch (\Exception $e) {
                    $payer = false;
                }

                $payer_bank = false;
                if ($payer) {
                    try {
                        $payer_bank = OrganizationsSupport::createBankByBRSO($d->ПлательщикБИК, $d->ПлательщикРасчСчет, $payer->id);
                    } catch (\Exception $e) {
                        $payer_bank = false;
                    }
                }

                try {
                    $recipient = OrganizationsSupport::createOrgByInn($d->ПолучательИНН);
                } catch (\Exception $e) {
                    $recipient = false;
                }

                $recipient_bank = false;
                if ($recipient) {
                    try {
                        $recipient_bank = OrganizationsSupport::createBankByBRSO($d->ПолучательБИК, $d->ПолучательРасчСчет, $recipient->id);
                    } catch (\Exception $e) {
                        $recipient_bank = false;
                    }
                }

                $pay_in = 0;
                $date_pay = date("Y-m-d", strtotime($d->ДатаСписано));
                if ($d->ПолучательИНН == $data->organizations_bank->organization->inn) {
                    $date_pay = date("Y-m-d", strtotime($d->Дата));
                    $pay_in = 1;
                }

                $recipient_id = $recipient ? $recipient->id : 0;
                $payer_id = $payer ? $payer->id : 0;
                $hash = md5($d->Номер . $date_pay . $recipient_id . $payer_id . $d->Сумма);
                $module = $item->destination_type;

                $BankDetail = BankDetail::firstOrNew(["hash" => $hash]);
                if (!$BankDetail->exists) {
                    $BankDetail->date_create = date("Y-m-d", strtotime($d->Дата));
                }
                $BankDetail->numb = $d->Номер;
                $BankDetail->type = $d->type;
                $BankDetail->date = $date_pay;
                $BankDetail->pay_in = $pay_in;
                $BankDetail->sum = $d->Сумма;
                if ($payer) {
                    $BankDetail->payer = $payer->id;
                }
                if ($payer_bank) {
                    $BankDetail->payer_bank = $payer_bank->id;
                }
                if ($recipient) {
                    $BankDetail->recipient = $recipient->id;
                }
                if ($recipient_bank) {
                    $BankDetail->recipient_bank = $recipient_bank->id;
                }
                $BankDetail->comment = $d->НазначениеПлатежа;
                $BankDetail->upload_id = $BankUpload->id;
                $BankDetail->status = 1;
                $BankDetail->module = $module;

                $BankDetail->org = $data->organizations_bank->organization->org->id;
                $BankDetail->organizations_bank_id = $data->organizations_bank->id;

                $BankDetail->save();

                if ($BankDetail->wasRecentlyCreated) {
                    $balance = new \Economic\Balance;
                    $bd = static::getEBD($BankDetail);
                    $balance->oid = $bd->oid;
                    $balance->action = $bd->action;
                    $balance->org = $data->organizations_bank->organization->org->id;
                    $balance->date = $date_pay;
                    $balance->value = $d->Сумма;
                    $balance->pid = $BankDetail->id;
                    $balance->module = "bank";
                    $balance->create();
                }

                if (isset($item->select) && is_array($item->select)) {
                    foreach ($item->select as $is) {
                        if (!empty($is->value)) {
                            if ($BankDetail->wasRecentlyCreated) {
                                $sum = empty($is->total) ? 0 : $is->total;
                                $BankPay = BankPay::create(["bank_detail_id" => $BankDetail->id, "pid" => $is->value, "sum" => $sum]);

                                if ($module == "sales" && !in_array($is->value, $sales_a)) {
                                    $sales_a[] = $is->value;
                                }
                            }
                        }
                    }
                }
            }

            if (count($sales_a) > 0) {
                foreach ($sales_a as $sales_id) {
                    $sales = Sales::find($sales_id);
                    static::sales_pay($sales);
                    \Sales\Support\SalesSupport::status_work($sales_id, ["sales" => $sales]);
                }
            }
        }
    }

    private static function sales_pay($sales)
    {
        $attr = [];
        $attr['uid'] = $sales->uid;
        $attr['icon'] = config('app.url') . '/files/push/comment.png';
        $attr['url'] = config('app.url') . '/administration/module/sales/order/' . $sales->id;
        $attr['pNotif'] = true;
        $attr['pPush'] = true;
        $attr['module'] = "sales";
        $attr['pid'] = $sales->id;
        $attr['title'] = 'Оплата заказа';

        $sales = static::sales_pay_status($sales);

        switch ($sales->pay) {
            case 1:
                $attr['text'] = "Заказ №{$sales->numb} оплачен частично";
                break;
            case 2:
                $attr['text'] = "Заказ №{$sales->numb} оплачен полностью";
                break;
        }

        if ($sales->pay != 0) {
            sendAllNotif($attr);
        }
    }

    static function sales_pay_status($sales)
    {
        $total_pay = BankPay::getTotal("sales", $sales->id);
        $pay = BankPay::getPayStatus($sales->total, $total_pay);
        $sales->pay = $pay;
        $sales->save();
        return $sales;
    }

    // Economic balance data
    private static function getEBD($BankDetail)
    {
        if ($BankDetail->pay_in == 0) {
            return (object)[
                "oid" => empty($BankDetail->recipient) ? 123 : $BankDetail->recipient,
                "action" => "plus",
            ];
        } else {
            return (object)[
                "oid" => $BankDetail->payer,
                "action" => "minus",
            ];
        }
    }

    private static function validationFile(Request $request)
    {
        if ($request->hasFile('file')) {
            return $request->file('file');
        } else {
            throw new \Exception('Файл не найден');
        }
    }

    static function getPactTemplate($id)
    {
        $relations = new \Relations;
        $data = $relations->find(["module_in" => "reestr", "module_out" => "bank_statement", "pid_out" => $id]);
        return count($data) > 0 ? \Reestr\ReestrModule::pact_template($data[0]["pid_in"]) : "Договор не выбран!";
    }

    static function getReestrModule($id)
    {
        $ReestrModule = new \Reestr\ReestrModule;
        $ReestrModule->model_out = \Bank\Models\BankDetail::class;
        $ReestrModule->module_out = "bank_statement";
        $ReestrModule->pid_out = $id;
        $ReestrModule->oid_field = "payer";
        $ReestrModule->local_script = <<<HTML
		    reestr_module.update_head = function(){
		    	pay.pact_load();
		    };
		    reestr_module.after_save = async function(){
		    	await (await fetch(`/administration/module/bank/recount/{$id}`)).text();
		    }
HTML;
        return [
            "modal" => $ReestrModule->modal(),
            "script" => $ReestrModule->script(),
        ];
    }

    static function edit($id)
    {
        $bank_detail = BankDetail::findOrFail($id);

        //display('@bank/manager/edit.html.twig', [
        return view('bank::manager.edit', [
            "id" => $id,
            "bank_detail" => $bank_detail,
            "pay_in_list" => BankDetail::getPayIn(),
            "type_list" => BankDetail::getType(),
            "payer_select" => static::getSelect([
                "name" => "payer",
                "ajaxUrl" => "getOrganizations",
                "id" => $bank_detail->payer,
            ]),
            "recipient_select" => static::getSelect([
                "name" => "recipient",
                "ajaxUrl" => "getOrganizations",
                "id" => $bank_detail->recipient,
            ]),
            "bank_pay_total" => BankPay::getTotalDetail($id),
            "bank_pay" => BankPay::where("bank_detail_id", $id)->get(),
            "select_sales" => static::getSelect([
                "name" => "select_sales",
                "ajaxUrl" => "getSalesToSelect",
                "select_class" => "sales_select"
            ]),
            "select_supply_order" => static::getSelect([
                "name" => "select_supply_order",
                "ajaxUrl" => "getSupplyOrderToSelect",
                "select_class" => "supply_order_select"
            ]),

            "pact" => static::getPactTemplate($id),
            "reestr_module" => static::getReestrModule($id),
        ]);
    }

    static function edit_details($id)
    {
        display('@bank/manager/edit_details.html.twig', [
            "id" => $id,
            "bank_detail" => BankDetail::findOrFail($id),

            "bank_pay_total" => BankPay::getTotalDetail($id),
            "bank_pay" => BankPay::where("bank_detail_id", $id)->get(),
        ]);
    }

    static function edit_reestr($id)
    {
        display('@bank/manager/edit_reestr.html.twig', [
            "id" => $id,
            "bank_detail" => BankDetail::findOrFail($id),

            "pact" => static::getPactTemplate($id),
            "reestr_module" => static::getReestrModule($id),
        ]);
    }

    static function update($id, Request $request)
    {
        $BankDetail = BankDetail::findOrFail($id);
        $BankDetail->update($request->all());
        echo $BankDetail->toJson();
    }

    static function recount($id)
    {
        $bank = \Bank\Models\BankDetail::find($id);
        $bd = static::getEBD($bank);
        $BalanceRecount = new \Economic\BalanceRecount;
        $BalanceRecount->org = $bank->org;
        $BalanceRecount->oid = $bd->oid;
        $BalanceRecount->date_start = $bank->date;
        $BalanceRecount->recount();
    }
}
