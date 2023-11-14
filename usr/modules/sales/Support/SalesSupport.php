<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/


namespace Sales\Support;

use Boot\Models\Users;
use Sales\Models\Sales;
use Boot\Support\Carbon;
use Boot\Support\Facades\Auth;
use Boot\Support\Facades\Cache;
use Boot\Support\Facades\Config;

class SalesSupport
{

    protected static function getSalesKey($type, $uid = null)
    {
        if ($uid == null) {
            $uid = Auth::id();
        }
        return 'sales.user.' . $uid . ($type ? '.' . $type : '');
    }

    public static function getSalesActiveCount($uid = null)
    {
        if ($uid == null) {
            $uid = Auth::id();
        }
        $key = static::getSalesKey('active.count', $uid);
        return Cache::rememberForever($key, function () use ($uid) {
            return Sales::whereIn('end', [0, 3])->where('uid', $uid)->count();
        });
    }

    public static function reloadSalesActiveCount($uid = null)
    {
        if ($uid == null) {
            $uid = Auth::id();
        }
        $key = static::getSalesKey('active.count', $uid);
        Cache::forget($key);
        return static::getSalesActiveCount($uid);
    }

    public static function getSalesCompleteTotal($uid = null, $between = null)
    {
        if ($uid == null) {
            $uid = Auth::id();
        }
        if ($between == null) {
            $between = [
                'from' => Carbon::now()->startOfMonth(),
                'to' => Carbon::now()
            ];
        }
        $key = static::getSalesKey('active.total.user', $uid);
        return Cache::remember($key, 3600, function () use ($uid, $between) {
            return Sales::whereIn('end', [1])->whereBetween('end_date', $between)->where('uid', $uid)->sum('total');;
        });
    }

    public static function reloadSalesCompleteTotal($uid = null)
    {
        if ($uid == null) {
            $uid = Auth::id();
        }
        $key = static::getSalesKey('active.total.user', $uid);
        Cache::forget($key);
        return static::getSalesActiveCount($uid);
    }

    public static function getSalesRating($uid = null, $between = null)
    {
        if ($uid == null) {
            $uid = Auth::id();
        }
        $key = 'sales.user.rating';
        $rating = Cache::remember($key, 3600, function () use ($uid, $between) {
            $users = Users::where('group', 1)->where('active', 1)->get();

            $totalToUsers = [];
            foreach ($users as $user) {
                $totalToUsers[] = [
                    'uid' => $user->id,
                    'total' => static::getSalesCompleteTotal($user->id, $between)
                ];
            }
            usort($totalToUsers, function ($a, $b) {
                if ($a['total'] == $b['total']) {
                    return 0;
                }
                return ($a['total'] > $b['total']) ? -1 : 1;
            });
            $result = [];
            foreach ($totalToUsers as $key => $value) {
                $result[$value['uid']] = $key;
            }

            return $result;
        });
        return $rating[$uid];
    }

    public static function reloadSalesRating($uid = null, $between = null)
    {
        if ($uid == null) {
            $uid = Auth::id();
        }
        $key = 'sales.user.rating';
        Cache::forget($key);
        return static::getSalesRating($uid, $between);
    }


    //отправка коммента в supply_order при подтверждении
    static function send_comment_supply_order($id, $module, $pid, $comment)
    {
        if (!empty($comment)) {
            $uid = Auth::id();
            $supply_order = \Supply\Model\SupplyOrder::findOrFail($id);
            if (!empty($supply_order)) {
                $comment_control = is_array($supply_order->comment_control) ? $supply_order->comment_control : [];
                $comment_control[] = [
                    "module" => $module,
                    "pid" => $pid,
                    "uid" => $uid,
                    "date" => Carbon::now(),
                    "comment" => $comment,
                ];
                $supply_order->comment_control = $comment_control;
                $supply_order->save();

                $attr = [];
                $attr['uid'] = [$supply_order->uid];
                $attr['title'] = 'Заявка №' . $supply_order->numb . ' на закупку была отправлена на доработку!';
                $attr['text'] = 'Заявка №' . $supply_order->numb . ' на закупку была отправлена на доработку, нажмите чтобы посмотреть!';
                $attr['icon'] = Config::get('global.url') . '/files/push/new_order.png';
                $attr['url'] = Config::get('global.url') . '/administration/module/supply/order_detail/' . $id;
                $attr['pNotif'] = true;
                $attr['pPush'] = true;
                $attr['module'] = "supply_order";
                $attr['pid'] = $id;
                sendAllNotif($attr);
            }
        }
    }

    static function get_transfer_active_count($sdid)
    {
        if (empty($sdid)) {
            return 0;
        } else {
            return \Storage\Model\StockTransferDetail::whereHas("stock_transfer", function ($query) {
                $query->where("active", 1);
            })->where("sdid", $sdid)->sum("count");
        }
    }

    static function get_reserve($request)
    {
        $data = [];
        $sd = [];
        if (!empty($request["sales_id"])) {
            $sales_detail = \Sales\Model\SalesDetail::where("sales_id", $request["sales_id"])->get();
            foreach ($sales_detail as $item) {
                $sd[] = $item;
            }
        }
        if (!empty($request["id"])) {
            if (is_array($request["id"])) {
                $sd = \Sales\Model\SalesDetail::find($request["id"]);
            } else {
                $sd[] = \Sales\Model\SalesDetail::find($request["id"]);
            }
        }

        foreach ($sd as $i) {
            $sales = $i->sales;
            $ndr = get_ndr(["cid" => $i->cid, "stock_id" => $sales->stock_id, "module" => "sales", "pid" => $i->id, "returnType" => "array"]);
            $data[] = [
                "id" => $i->id,
                "ndr" => $ndr,
            ];
        }
        return $data;
    }

    static function smart_reserve($id): array
    {
        $data = [];
        $data["action"] = false;
        $uid = Auth::id();
        $sd = \Sales\Model\SalesDetail::find($id);
        $sales = $sd->sales;
        $ndr = get_ndr(["cid" => $sd["cid"], "stock_id" => $sales["stock_id"], "module" => "sales", "pid" => $id, "returnType" => "array"]);
        if (($ndr["r_this"] + $ndr["r_after_this"]) == 0) {
            $data["action"] = "add";
        } else {
            $groups_allow = getGroup(["group" => "admin"]);
            // $groups_allow = [];
            $groups_allow[] = $sales["uid"];

            if (in_array($uid, $groups_allow)) {
                $data["action"] = "delete";
            }
        }
        return $data;
    }

    static function get_count_realization_left($id, $option = [])
    {
        if ($id) {
            $sales_detail = $option["sales_detail"] ?? \Sales\Model\SalesDetail::find($id);
            $count = $sales_detail->count;

            $realization_count = \Realization\Model\RealizationDetail::whereHas('realization', function ($query) {
                $query->where('active', 1);
            })->where("sdid", $id)->sum("count");

            return $count - $realization_count;
        }
        return 0;
    }

    static function get_purchase_price_date_sales($id, $cid = null)
    {
        $priceData = \Sales\Model\SalesDetail::getPurchase($id, ["cid" => $cid]);
        $data = [
            "sales_detail" => $priceData->sales_detail,
            "supply_order_detail" => $priceData->supply_order_detail,
            "sos" => $priceData->sos,
            "cid" => $cid,
        ];
        $dateData = \Sales\Model\SalesDetail::getDatePost($id, $data);
        $fileData = \Sales\Model\SalesDetail::getPurchaseFile($id, $data);
        return [
            "flagSup" => !empty($fileData->file_url),
            "price" => $priceData->price,
            "datePrice" => $priceData->date,
            "color" => $priceData->color,
            "date_post" => $dateData->date_post,
            "dateColor" => $dateData->color,
            "fileUrl" => $fileData->file_url,
        ];
    }

    static function check_assembly($id)
    {
        $sync = [];
        $items = \Sales\Model\SalesDetail::withTrashed()->where("sales_id", $id)->get();
        foreach ($items as $item) {
            $sdid = $item->id;
            $s_name = $item->s_name;
            $s_del = $item->del;
            $s_count = $item->count;
            $assembly_items = \Storage\Model\StorageAssemblyDetail::with("storage_assembly")->where("sdid", $sdid)->whereIn([1, 2])->get();
            if ($assembly_items->count() > 0) {
                $count_assembly = 0;
                foreach ($assembly_items as $assembly) {
                    $a_count = $assembly->count;
                    switch ((int)$assembly->type) {
                        case 1:
                            $count_assembly += $a_count;
                            break;
                        case 2:
                            $count_assembly -= $a_count;
                            break;
                    }
                }

                //проверка на удаление/замену
                if ($s_del == 1 && $count_assembly != 0) {
                    //minus
                    $sync[$sdid] = [
                        "s_name" => $s_name,
                        "count" => $count_assembly,
                        "type_assembly" => 2,
                    ];
                    //проверка на изменение количества
                } else if ($count_assembly != $s_count) {
                    if ($count_assembly > $s_count) {
                        //minus
                        $sync[$sdid] = [
                            "s_name" => $s_name,
                            "count" => ($count_assembly - $s_count),
                            "type_assembly" => 2,
                        ];
                    }
                    if ($count_assembly < $s_count) {
                        //plus
                        $sync[$sdid] = [
                            "s_name" => $s_name,
                            "count" => ($s_count - $count_assembly),
                            "type_assembly" => 1,
                        ];
                    }
                }
            }
        }
        return $sync;
    }

    static function sales_type_list_view()
    {
        $list = \Sales\Model\Sales::getSalesType();
        $result = [];
        foreach ($list as $k => $l) {
            if ($k != 0)
                $result[] = [
                    "value" => $k,
                    "text" => $l,
                ];
        }
        return $result;
    }

    static function status_work($sales_id, $request = [])
    {
        $sales = $request["sales"] ?? \Sales\Model\Sales::findOrFail($sales_id);
        if ($sales->end != 1 && $sales->end != 2) {
            $sales->end = 3;
            $sales->save();
        }
    }

    static function check($sales_id)
    {
        $sales = \Sales\Model\Sales::findOrFail($sales_id);
        $result = [];

        if (empty($sales->uid)) {
            $result[] = [
                "code" => 2,
                "message" => "Ответственный отсутствует",
            ];
        }
        if (empty($sales->object)) {
            $result[] = [
                "code" => 3,
                "message" => "Объект отсутствует",
            ];
        }
        if (empty($sales->oid)) {
            $result[] = [
                "code" => 4,
                "message" => "Организация отсутствует",
            ];
        }

        return $result;
    }

    static function list_messages($sales_id)
    {
        $sales = \Sales\Model\Sales::find($sales_id);
        return static::list_messages_oid($sales->oid);
    }

    static function list_messages_oid($oid)
    {
        $data = [];
        foreach (\Boot\Models\UsersOrg::with("user")->where("oid", $oid)->get() as $item) {
            $data[] = (object)[
                "users_org" => $item,
                "messengers" => [
                    (object)["name" => "SMS",    "field" => "sms",      "status" => !empty($item->user->phone)],
                    (object)["name" => "E-mail", "field" => "email",    "status" => !empty($item->user->email)],
                    (object)["name" => "Viber",  "field" => "viber",    "status" => \Boot\Models\UsersToken::where("uid", $item->uid)->where("platform", "viber")->exists()],
                    (object)["name" => "VK",     "field" => "vk",       "status" => \Boot\Models\UsersToken::where("uid", $item->uid)->where("platform", "vk")->exists()],
                ],
            ];
        }
        return $data;
    }

    static function send_message($sales_id, $uid, array $role)
    {
        if (count($role) > 0) {
            $sales = \Sales\Model\Sales::find($sales_id);
            $attr = [];
            $attr['uid'] = $uid;
            $attr['title'] = 'Информация по заказу №' . $sales->numb;
            $attr['text'] = 'Чтобы получить информацию по вашему заказу, перейдите по ссылке.';
            $attr['url'] = "https://lk.atri-energo.ru/e-commerce/orders/{$sales_id}/order";
            $attr['url_pass'] = config('app.url') . '/sales/preview/' . $sales->md5;
            $manager = \Boot\Models\Users::find($sales->uid);
            $attr['manager_fio'] = $manager->full_user_name;
            $manager_dop = '+7 (3412) 908-123';
            $attr['manager_email'] = $manager->email;
            $attr['manager_phone'] = $manager->phone;
            $managerPhone = \Boot\Models\UsersPhoneid::where("uid", $sales->uid)->where("status", 1)->first();
            if (!empty($users_phone)) {
                $manager_dop .= ' доп. ' . $managerPhone->phoneid;
            }
            $attr['manager_dop'] = $manager_dop;
            $org = getOrgInfo($sales->org);
            $manager_post = 'Сотрудник компании';
            $managerOrgPost = \Boot\Models\UsersOrg::where("uid", $sales->uid)->where("oid", $org['org']['oid'])->first();
            if (!empty($managerOrgPost)) {
                $manager_post = $managerOrgPost->post;
            }
            $attr['manager_post'] = $manager_post;
            $user = \Boot\Models\Users::find($uid);

            $pdf_get = file_get_contents(config('app.url') . '/sales/print/bill_sign/' . $sales_id . '&format=pdf&autoprint=false');
            $pdfurl = config('app.url') . '/files/sales/' . $sales_id . '/bill_sign_' . $sales->numb . '_' . date("d-m-Y", strtotime($sales->date)) . '.pdf';
            $attr['pdf'] = $pdfurl;
            $md5_recovery = mb_substr(md5($user->email . 'eduard_laas_loh'), 0, 10);
            $attr['text_email'] = 'Здравствуйте ' . $user->short_user_name . '<br>
            <b style="font-size: 18px; line-height: 35px; ">Ваш заказ #' . $sales->numb . ' успешно сформирован!</b><br>Вы может ознакомится с ним использую ваш личный кабинет перейдя по ссылке ниже или воспользоваться прикрепленным во вложении счетом. Если у вас нет данных для входа в личный кабинет вы можете получить их перейдя по следующей <a href="https://atri-energo.ru/profile/recoveryPass/' . $uid . '/' . $md5_recovery . '" target="_blank">ссылке.';
            foreach ($role as $val) {
                switch (mb_strtolower($val)) {
                    case "sms":
                        sendSms($attr);
                        break;
                    case "email":
                        sendEmailSales($attr);
                        break;
                    case "viber":
                        sendViber($attr);
                        break;
                    case "vk":
                        sendVk($attr);
                        break;
                }
            }
        }
    }

    static function end_sales_middleware($sales_id)
    {
        $sales = \Sales\Model\Sales::findOrFail($sales_id);
        $result = ["status" => true, "error" => []];

        if ($sales->sales_type != 2 && $sales->sales_type != 5) {
            if (is_null($sales->market_segment)) {
                $result["status"] = false;
                $result["error"][] = "Выберите сегмент рынка";
            }
            if (is_null($sales->by_field_activity)) {
                $result["status"] = false;
                $result["error"][] = "Выберите сферу деятельности";
            }
        }

        return $result;
    }

    static function check_all_realization_and_transfer($sales_id)
    {
        $sales = \Sales\Model\Sales::findOrFail($sales_id);
        $type = empty($sales->sales_type) ? 1 : $sales->sales_type;
        switch ($type) {
            default:
            case 1:
                return static::check_all_realization($sales_id);
                break;
            case 2:
                return static::check_all_transfer($sales_id);
                break;
        }
    }

    static function check_all_realization($sales_id)
    {
        $sales = static::get_sales_count_and_id($sales_id);
        $sales_list = $sales["sales_list"];
        $sales_list_items = $sales["sales_list_items"];

        if (count($sales_list_items) > 0) {
            foreach (\Realization\Model\RealizationDetail::whereHas("realization", function ($query) {
                $query->where("active", 1);
            })->whereIn("sdid", $sales_list_items->modelKeys())->get() as $item) {
                $sales_list[$item->sdid] -= $item->count;
                if ($sales_list[$item->sdid] <= 0) {
                    unset($sales_list[$item->sdid]);
                }
            }
            return $sales_list;
        } else {
            return [];
        }
    }

    static function check_all_transfer($sales_id)
    {
        $sales = static::get_sales_count_and_id($sales_id);
        $sales_list = $sales["sales_list"];
        $sales_list_items = $sales["sales_list_items"];
        if (count($sales_list_items) > 0) {
            foreach (\Storage\Model\StockTransferDetail::whereHas("stock_transfer", function ($query) {
                $query->where("active", 1);
            })->whereIn("sdid", $sales_list_items->modelKeys())->get() as $item) {
                if (isset($sales_list[$item->sdid])) {
                    $sales_list[$item->sdid] -= $item->count;
                    if ($sales_list[$item->sdid] <= 0) {
                        unset($sales_list[$item->sdid]);
                    }
                }
            }
            return $sales_list;
        } else {
            return [];
        }
    }

    static function get_sales_count_and_id($sales_id)
    {
        $sales_list = [];
        $sales_list_id = [];
        $sales_list_items = \Sales\Model\SalesDetail::where("sales_id", $sales_id)->get();
        foreach ($sales_list_items as $item) {
            $sales_list[$item->id] = $item->count;
            $sales_list_id[] = $item->id;
        }
        return [
            "sales_list" => $sales_list,
            "sales_list_id" => $sales_list_id,
            "sales_list_items" => $sales_list_items,
        ];
    }

    static function end_sales($sales_id, $end, $comment_end = null)
    {
        $end_date = $end == 0 ? null : \Carbon\Carbon::now();
        $sales = \Sales\Model\Sales::find($sales_id);
        $sales->comment_end = $comment_end;
        $sales->end = $end;
        if (empty($sales->end_date)) $sales->end_date = $end_date;
        $sales->save();

        $reserv = new \Reserv();
        foreach ($sales->sales_detail as $item) {
            $reserv->del_reserv($item->id, "sales");
        }
        return $sales;
    }

    static function fast_change_sales($id, $field, $value)
    {
        $sales = \Sales\Model\Sales::findOrFail($id);
        $change_numb = function ($date) use (&$sales) {
            $numb_sales_array = \Boot\Support\Module::numb('sales', $sales->org, $date);
            $sales->numb = $numb_sales_array['numb'];
            $sales->numb_counter = $numb_sales_array['numb_counter'];
        };

        switch ($field) {
            default:
                $sales->{$field} = $value;
                break;
            case "org":
                $orgInfo = getOrgInfo($value);
                $sales->{$field} = $value;
                //замена номера продажи
                $change_numb($sales->date);
                $sales->nds_includes = $orgInfo["org"]["nds_includes"];
                $sales->stock_id = empty($orgInfo["org"]["stock_id_default"]) ? null : $orgInfo["org"]["stock_id_default"];

                //замена номера реализации
                foreach (\Realization\Model\Realization::where("sales_id", $id)->where("active", 0)->get() as $item) {
                    $numb_realization_array = \Boot\Support\Module::numb('realization', $value, $item->date);
                    $item->numb = $numb_realization_array['numb'];
                    $item->numb_counter = $numb_realization_array['numb_counter'];
                    $item->org = $value;
                    $item->save();
                }
                //изменение ндс
                \Sales\Support\SalesDetailSupport::recount_sales($id);
                break;
            case "date":
                $old_year = date("Y", strtotime($sales->date));
                $new_year = date("Y", strtotime($value));
                if ($old_year != $new_year) {
                    $change_numb($value);
                }
                $sales->{$field} = $value;
                break;
            case "status_client":
                $sales->{$field} = $value;
                switch ($value) {
                    case 3:
                        \Sales\Model\SalesDetail::where("sales_id", $id)->update(["status_client" => 3]);
                        if (!empty($sales->uid_send)) {
                            $attr = [];
                            $attr['uid'] = [$sales->uid_send];
                            $attr['title'] = "Заказ согласован.";
                            $attr['text'] = "Ваш заказ №{$sales->numb} согласован. Вы можете найти его в списке заказов или перейдя по ссылке.";
                            $attr['icon'] = config('app.url') . '/files/push/new_storage.png';
                            $attr['url'] = "http://lk.atri-energo.ru/e-commerce/orders/{$sales->id}/order";
                            $attr['pNotif'] = true;
                            $attr['pPush'] = true;
                            $attr['module'] = "sales";
                            $attr['pid'] = $sales->id;
                            sendAllNotif($attr);
                        }
                        break;
                }
                break;
        }

        $sales->save();
        return $sales;
    }

    static function supply_order_send_control($sales_id, $supply_order_id, $status, $comment)
    {
        switch ($status) {
            case 2:
                (new \Module)->supply_to_control($supply_order_id);
                break;
            case 3:
                static::send_comment_supply_order($supply_order_id, "sales", $sales_id, $comment);
                break;
        }
        $sales = \Sales\Models\Sales::findOrFail($sales_id);
        $confirm_supply_order = $sales->confirm_supply_order;
        if (($key = array_search($supply_order_id, $confirm_supply_order)) !== FALSE) {
            unset($confirm_supply_order[$key]);
        }
        $sales->update(["confirm_supply_order" => $confirm_supply_order]);
        return [
            "sales" => $sales,
        ];
    }

    static function status_client_send_control($sales_id, $status)
    {
        $status = intval($status);
        $sales = \Sales\Models\Sales::findOrFail($sales_id);
        $sales->update(["status_client" =>  $status]);
        return [
            "sales" => $sales,
        ];
    }

    static function getPayDetail($sales_id): object
    {
        $items = \Bank\Models\BankPay::with("bank_detail")->whereHas("bank_detail", function ($query) {
            $query->where("module", "sales");
        })->where("pid", $sales_id)->get();
        if ($items->count() > 0) {
            $template = "";
            foreach ($items as $item) {
                $organizations = \Organizations\Model\Organizations::whereIn("id", [$item->bank_detail->payer, $item->bank_detail->recipient])->get();
                foreach ($organizations as $o) {
                    switch ($o->id) {
                        case $item->bank_detail->payer:
                            $payer = $o->name;
                            break;
                        case $item->bank_detail->recipient:
                            $recipient = $o->name;
                            break;
                    }
                }
                $template .= '<div class="mb20">
                    <b>От ' . $payer . ' к ' . $recipient . '. Дата ' . date("d.m.Y", strtotime($item->bank_detail->date)) . '. Сумма ' . number_format($item->sum, 2, ',', ' ') . '
                        <a href="/administration/module/bank/' . $item->id . '/edit" target="_blank" class="fa fa-external-link"></a>
                    </b>
                    <br>' . $item->bank_detail->comment . '
                </div>';
            }
        } else {
            $template = 'Счета отсутствуют';
        }
        return (object)[
            "template" => $template,
            "items" => $items,
        ];
    }

    static function re_total($id)
    {
        $data = \Sales\Model\SalesDetail::where("sales_id", $id)->first([
            \DB::raw("SUM(total) as total"),
            \DB::raw("SUM(nds) as nds"),
        ]);
        $sales = \Sales\Model\Sales::findOrFail($id);
        $sales->update([
            "total" => round($data->total, 2),
            "nds" => round($data->nds, 2),
        ]);
        return $sales;
    }
}
