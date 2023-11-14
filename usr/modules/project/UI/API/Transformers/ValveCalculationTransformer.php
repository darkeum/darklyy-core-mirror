<?php

namespace Calculation\UI\API\Transformers;

use \Boot\Abstracts\Transformers\Transformer;

class ValveCalculationTransformer extends Transformer
{
    protected $availableIncludes = [

    ];

    protected $defaultIncludes = [

    ];

    public function transform(): array
    {
        return [
            'object' => '',
            'id' => '',
            'name' => '',
            'description' => '',
            'display_name' => '',
        ];
    }
}
