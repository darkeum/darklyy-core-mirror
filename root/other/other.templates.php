<?php

use Boot\Support\Currency;

/**
 * @name        JMY CORE
 * @link        https://jmy.su/
 * @copyright   Copyright (C) 2012-2017 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 * @info_ru     Место под ваши собственные функции для шаблонов
 * @info_en     Place for your own functions for templates
 */
$uid = $core->auth->user_info['id'];
// $query_so = $db->query("SELECT id FROM " . DB_PREFIX . "_supply_order WHERE status = '2'");

$count_recount = \Storage\Model\Storage::where("status", 1)->count();
$count_assembly = \Storage\Model\StorageAssembly::where("del", 0)->where("status", 1)->count();
$count_transfer = \Storage\Model\StockTransfer::where("active", 0)->where("del", 0)->count();
$count_delivery_storage = \Logistics\Model\LogisticsDelivery::where("status", 2)->count();
$count_storage = $count_recount + $count_assembly + $count_transfer + $count_delivery_storage;
$this->setVar('TASK_COUNT', \Task\Model\Task::where("toid", $uid)->where("status", "!=", 3)->count());
$this->setVar('RECOUNT_COUNT', $count_recount);
$this->setVar('ASSEMBLY_COUNT', $count_assembly);
$this->setVar('STORAGE_COUNT', $count_storage);
$this->setVar('TRANSFER_COUNT', $count_transfer);
$this->setVar('DELIVERY_STORAGE', $count_delivery_storage);

$count_type_1 = \Supply\Model\Supply::where("status", 1)->where("type", 1)->count();
$count_type_2 = \Supply\Model\Supply::where("status", 1)->where("type", 2)->count();
// $count_type_3 = \Supply\Model\Supply::where("status", 2)->count();
$count_type_all = \Supply\Model\Supply::where("status", 1)->count();
$this->setVar('SUPPLY_COUNT', $count_type_all);
$this->setVar('SUPPLY_COUNT_TYPE_1', $count_type_1);
$this->setVar('SUPPLY_COUNT_TYPE_2', $count_type_2);
// $this->setVar('SUPPLY_COUNT_TYPE_3', $count_type_3);

//$this->setVar('FAST_CART', callAddProduct('0', "0"));
$order_count = \Order\Models\Order::where('read_status', 0)->count();
$client_all_count =  $order_count;
$this->setVar('CLIENT_COUNT', $client_all_count);
$this->setVar('ORDER_COUNT', $order_count);

$fcontrol = \Fcontrol\Model\Control::where("status", 1)->count();
$fcontrol_pay = \Fcontrol\Model\Control::where("status", 3)->where("pay", 0)->count();
$this->setVar('FCONTROL', $fcontrol);
$this->setVar('FCONTROL_PAY', $fcontrol_pay);
$this->setVar('FCONTROL_ALL', $fcontrol + $fcontrol_pay);


$us =  Currency::get_exchange()->get(840);
$us_old = Currency::get_exchange(date("Y-m-d", strtotime('-1 day')))->get(840);
$us_dis = $us - $us_old;
$eur = Currency::get_exchange()->get(978);
$eur_old = Currency::get_exchange(date("Y-m-d", strtotime('-1 day')))->get(978);
$eur_dis = $eur - $eur_old;
$this->setVar('PRICE_US', $us);
$this->setVar('PRICE_US_DIS', number_format($us_dis, 4, '.', ' '));
$this->setVar('PRICE_US_DIS_COLOR', $us_dis > 0 ? "amount_middle__under-g" : "amount_middle__under-r");
$this->setVar('PRICE_EUR', $eur);
$this->setVar('PRICE_EUR_DIS', number_format($eur_dis, 4, '.', ' '));
$this->setVar('PRICE_EUR_DIS_COLOR', $eur_dis > 0 ? "amount_middle__under-g" : "amount_middle__under-r");
