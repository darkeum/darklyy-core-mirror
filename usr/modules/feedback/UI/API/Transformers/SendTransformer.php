<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/


namespace Promotions\UI\API\Transformers;

use Boot\Abstracts\Transformers\Transformer;
use Sales\Model\SalesDetail;

class PromotionDetailTransformer extends Transformer
{
     public function transform(SalesDetail $items): array
    {
        $ndr = get_ndr(["cid" => $items->cid, "stock_id" => "1", "returnType" => "array"]);
        $price = \Price::getPrice($items->cid);
        $discount_price = \Price::get_all(["price" => $items->s_price, "discount" => $items->discount]);
        $response = [
            'object' => $items->getResourceKey(),
            'id' => $items->id,
            'position' => $items->position,
            'cid' => $items->cid,
            'title' => $items->catalog->title,
            'sku' => $items->catalog->sku,
            'preview' => $items->catalog->preview,
            'count' => $ndr['d'],
            'price' => number_format($price->price_list,2,',',' '),
            'discount_price' => number_format($discount_price['discount_price'],2,',',' '),           
        ];

        return $response;
    }

}
