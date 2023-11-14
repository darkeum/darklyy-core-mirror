<?php

namespace Boot\System\Exceptions\Ignition\Http\Controllers;

use Spatie\Ignition\Config\IgnitionConfig;
use Boot\System\Exceptions\Ignition\Http\Requests\UpdateConfigRequest;

class UpdateConfigController
{
    public function __invoke(UpdateConfigRequest $request)
    {
        $result = (new IgnitionConfig())->saveValues($request->validated());

        return response()->json($result);
    }
}
