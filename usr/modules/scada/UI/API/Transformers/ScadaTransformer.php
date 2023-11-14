<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\UI\API\Transformers;

use Scada\Models\Scada;
use Boot\Abstracts\Transformers\Transformer;
use Scada\UI\API\Transformers\PromotionDetailTransformer;

class ScadaTransformer extends Transformer
{

    // protected $availableIncludes = [
    //     'detail',
    // ];

    public function transform(Scada $item): array
    {
        $response = [
            'object' => $item->getResourceKey(),
            'id' => $item->getHashedKey(),
            'real_id' => $item->id,
            'title' => $item->title,
            'address' => $item->address,
            'oid_name' => $item->organizations->name,
            'object' => $item->object,
            'read_at' => $item->read_at,
            'status' => $item->connection,         
        ];

        return $response;
    }

    // public function includeDetail(Scada $promotions)
    // {
    //     if (!empty($promotions->sales->sales_detail)) {
    //         return $this->collection($promotions->sales->sales_detail, new PromotionDetailTransformer());
    //     }
    //     return $this->null();
    // }
}
