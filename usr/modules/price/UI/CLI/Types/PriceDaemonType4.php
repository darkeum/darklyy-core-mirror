<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\UI\CLI\Types;

use Price\Parser\AbstractParser;
use Price\UI\CLI\PriceDaemonType;
use Price\Models\CatalogPriceRivalDiscount;

class PriceDaemonType4 extends PriceDaemonType
{
    protected $supplier_price = false;
    protected $competing_price = false;

    protected $price_type = null;

    public function parse()
    {
        $this->parse_supplier_price();
        $this->parse_competing_price();

        $this->parse_pd();
    }

    public function parse_supplier_price()
    {
        $link = $this->get_cpr_rival_link();
        $parser = AbstractParser::get_class_for_link(["link" => $link]);
        if ($parser) {
            $parser->url = $link;
            $parser->catalog = $this->cpr->catalog;
            $data = $parser->parse();
            if (empty($data["price"])) {
                $this->error_rival();
            } else {
                $this->supplier_price = $data;
            }
        } else {
            $this->error_parser_rival();
        }
    }

    public function parse_competing_price()
    {
        $items = [];
        $links = AbstractParser::get_classes_for_links($this->cpr->competing_links);
        if (count($links) > 0) {
            foreach ($links as $link => $parser) {
                if ($parser) {
                    $parser->url = $link;
                    $parser->catalog = $this->cpr->catalog;
                    $d = $parser->parse();
                    if (empty($d["price"])) {
                        $this->error_competing();
                    } else {
                        $host = $parser->get_host();
                        $cprd = CatalogPriceRivalDiscount::where("cid", $this->cpr->id)->where("host", $host)->first();
                        $discount = $cprd->discount ?? 0;
                        $items[] = [
                            "price" => $d["price"],
                            "discount" => $discount,
                            "host" => $host,
                        ];
                    }
                } else {
                    $this->error_parser_competing();
                }
            }
            if (count($items) > 0) {
                $this->competing_price = $items;
            }
        } else {
            throw new \Exception("Не указаны ссылки на конкурентов");
        }
    }

    public function parse_pd()
    {
        if ($this->supplier_price && $this->competing_price) {
            $this->pd = [
                "price_type" => $this->price_type,
                "supplier_price" => [
                    "price" => $this->supplier_price["price"],
                    "discount" => $this->cpr->our_supplier_discount,
                ],
                "competing_price" => [
                    "items" => $this->competing_price,
                ]
            ];
        }
    }
}
