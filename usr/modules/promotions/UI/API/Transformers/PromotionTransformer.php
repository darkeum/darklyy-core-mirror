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

use Promotions\Models\Promotions;
use Boot\Abstracts\Transformers\Transformer;
use Promotions\UI\API\Transformers\PromotionDetailTransformer;

class PromotionTransformer extends Transformer
{

    protected $availableIncludes = [
        'detail',
    ];

    public function transform(Promotions $promotions): array
    {
        $response = [
            'object' => $promotions->getResourceKey(),
            'id' => $promotions->getHashedKey(),
            'title' => $promotions->title,
            'short' => $promotions->short,
            'full' => $promotions->full,
            'description' => $promotions->description,
            'type' => $promotions->type,
            'preview' => $promotions->preview,
            'date_start' => $promotions->date_start,
            'date_end' => $promotions->date_end,
            'readable_date_start' => $promotions->date_start->diffForHumans(),
            'readable_date_end' => $promotions->date_end->diffForHumans(),
        ];

        return $response;
    }

    public function includeDetail(Promotions $promotions)
    {
        if (!empty($promotions->sales->sales_detail)) {
            return $this->collection($promotions->sales->sales_detail, new PromotionDetailTransformer());
        }
        return $this->null();
    }
}
