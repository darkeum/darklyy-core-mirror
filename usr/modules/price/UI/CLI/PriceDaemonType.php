<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\UI\CLI;

use Exception;
use Price\Models\Log;
use Boot\Support\Carbon;
use Price\UI\CLI\PriceDaemon;
use Price\Models\CatalogPrice;
use Price\Parser\AbstractParser;
use Price\Models\CatalogPriceType;
use Price\Models\CatalogPriceRival;
use Price\Exceptions\ExceptionPrice;
use Price\Models\CatalogPriceDetail;
use Price\Models\CatalogPriceDiscount;
use GuzzleHttp\Exception\ClientException;

abstract class PriceDaemonType
{

    public $cpr;        //catalog_price_rival
    public $pd = false; //parse data
    public $price_id;
    public $purchase_price_id;

    protected $price_auto = null;

    abstract public function parse();

    public function generate()
    {
        $flag = true;
        $cid = $this->cpr->id;
        try {
            $this->parse();
            if ($this->pd) {
                $PriceGenerateName = $this->get_class_name();

                //формирование цены и скидки
                $PriceGenerate = $this->price_generate($PriceGenerateName);
                // $PriceGenerate->calculate_view();

                // сохранение цены и скидок
                $price = $PriceGenerate->get_price();
                $price_purchase = $PriceGenerate->get_purchase();
                if ($price > 0) {
                    $other_price = !empty($this->pd["price_type"]) && $this->pd["price_type"] != PriceDaemon::$price_type;
                    $other_price_type = $other_price ? CatalogPriceType::find($this->pd["price_type"]) : null;
                    $other_purchase_type = $other_price ? $other_price_type->link : null;

                    $this->create_price($cid, $price, $this->price_id, $other_price, $other_price_type);
                    $this->create_price($cid, $price_purchase, $this->purchase_price_id, $other_price, $other_purchase_type);

                    $list = $PriceGenerate->get_discount_name_list();
                    foreach ($list as $type => $d) {
                        $discount = $PriceGenerate->$d;
                        CatalogPriceDiscount::action(["cid" => $cid, "type" => $type, "discount" => $discount]);
                    }
                    $save_data = $PriceGenerate->calculate_view_data();
                    $comment = [
                        "type" => $this->cpr->type,
                        "data" => $save_data,
                    ];
                    Log::create(["module" => "price", "pid" => $cid, "comment" => $comment]);
                    $catalog = $this->cpr->catalog;
                    $catalog->price_data = $comment;
                    $catalog->saveQuietly();
                }
            }
        } catch (Exception | ExceptionPrice | ClientException $error) {
            $flag = false;
            switch (get_class($error)) {
                default:
                    if ($this->cpr->iteration_count > 0) {
                        $this->send_notif_error($error->getMessage());
                    } else {
                        CatalogPriceRival::findOrFail($cid)->update([
                            "iteration" => Carbon::now(), "iteration_count" => $this->cpr->iteration_count + 1, 'received_at' => null
                        ]);
                    }
                    break;
                case ExceptionPrice::class:
                    $this->send_notif_error($error->getMessage());
                    break;
                case ClientException::class:
                    $this->send_notif_error($this->get_text_catalog_error("Ошибка сайта"));
                    break;
            }
        } finally {
            if ($flag) {
                $this->clear_iteration();
            }
        }
    }

    public function create_price($cid, $price, $price_id, $other_price, $other_price_type)
    {
        $add = [];
        $add["cid"] = $cid;
        if ($other_price) {
            $catalog_price = CatalogPrice::find($price_id);
            if ($catalog_price->price_type != $other_price_type->id || $catalog_price->daemon != 0) {
                $alt_price = CatalogPrice::where('work', 1)->where('price_type', $other_price_type->id)->first();
                // dump( $alt_price);
                if (empty($alt_price)) {
                    // dd('xxx');
                    $PriceDaemon = new PriceDaemon;
                    $PriceDaemon::$price_type = $other_price_type->id;
                    $PriceDaemon->prices_create();
                    $alt_price = $PriceDaemon->get_price();
                    $alt_price->update(["price_type" => $other_price_type->id, "daemon" => 0, "work" => 1]);
                }
                // dump($alt_price->id.' - type '.$alt_price->price_type);
                $price_id = $alt_price->id;
            }
            $add["price_id"] = $price_id;
            $add["sum"] = $price / $other_price_type->course;
        } else {
            $add["price_id"] = $price_id;
            $add["sum"] = $price;
        }
        CatalogPriceDetail::create($add);
    }

    public function send_notif_error($error_message)
    {
        $this->send_notif(["text" => $error_message]);
        $this->clear_iteration();
    }

    public function clear_iteration()
    {
        $this->cpr->update(["iteration" => null, "iteration_count" => 0]);
    }

    public function get_cpr_rival_link()
    {
        return ee($this->cpr->rival_link);
    }

    public function get_class_name()
    {
        return "\Price\UI\CLI\GenerateTypes\PriceGenerateType{$this->cpr->type}";
    }

    public function price_generate($PriceGenerateName)
    {
        if (class_exists($PriceGenerateName)) {
            $PriceGenerate = new $PriceGenerateName();
            $PriceGenerate->cpr = $this->cpr;
            $PriceGenerate->pd = $this->pd;
            $PriceGenerate->init();

            $PriceGenerate->calculate();
            return $PriceGenerate;
        } else {
            throw new \Exception("Не найден такой тип цены");
        }
    }

    public function get_pd()
    {
        return $this->pd;
    }

    public function get_local_price()
    {
        if (is_null($this->price_auto)) {
            return \Price::getOriginalPriceList($this->cpr->id);
        } else {
            return \Price::getOriginalPriceListByAuto($this->cpr->id, $this->price_auto);
        }
    }

    public function send_notif($request)
    {
        $attr = [];
        $attr['uid'] = [79, 786];
        $attr['title'] = 'Ошибка цен';
        $attr['text'] = $request["text"];
        $attr['icon'] = config('app.url') . '/files/push/new_storage.png';
        $attr['url'] =  route('admin.price.settings.edit', ['id' => $this->cpr->id]);
        $attr['pNotif'] = true;
        $attr['pPush'] = true;
        $attr['module'] = "price";
        $attr['pid'] = $this->cpr->id;
        sendAllNotif($attr);
    }

    public function get_text_error($text)
    {
        return $this->get_text_catalog_error("Ошибка! Цена у {$text} не найдена");
    }

    public function get_text_catalog_error($text)
    {
        return AbstractParser::get_text_catalog_error($text, $this->cpr->catalog);
    }

    //нас
    public function error_us()
    {
        throw new Exception($this->get_text_error("нас"));
    }

    //поставщика
    public function error_rival()
    {
        throw new Exception($this->get_text_error("поставщика"));
    }

    //конкурента
    public function error_competing()
    {
        throw new Exception($this->get_text_error("конкурента"));
    }

    public function error_parser_rival()
    {
        throw new Exception("Не найден парсер поставщика");
    }

    public function error_parser_competing()
    {
        throw new Exception("Не найден парсер конкурента");
    }
}
