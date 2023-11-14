<?php

namespace Boot\System\Validation\Rules;

use Boot\System\Validation\Rule;
use Boot\Support\Str;
use Boot\Support\Facades\Phone as SystemPhone;
use \Boot\System\Validation\Rules\Interfaces\ModifyValue;

class Phone extends Rule implements ModifyValue
{

    /** @var string */
    protected $message = "The :attribute minimum is :min";

    public function check($value): bool
    {
        // dd(->formatInternational());
        if (Str::isPhone($value)) {
            return true;
        }
        return false;
    }

    public function modifyValue($value)
    {
        $phone = SystemPhone::make($value, ['AUTO', 'RU']);
        $response = [
            'source' => $value,
            'type' =>  $phone->getType() == 'mobile' ? 'Мобильный' : 'Стационарный',
            'phone' => $phone->formatInternational(),
            'country_code' => $phone->lib->getRegionCodeForNumber(),
            'city_code' => "999",
            'number' => "4815699",
            'extension' => null,
            'provider' => "ПАО МегаФон",
            'country' => "Россия",
            'region' => "Ростовская область",
            'city' => null,
            'timezone' => "UTC+3",
            'qc_conflict' => 0,
            'qc' => 0,
        ];
        dd($response);
        // $dadata = new DadataClient('ddeafb9ff43e79578243d8f031f6e9d8b047ca0e', 'e03a8f8ceaa114f8d8e0e09af92a0f3b069f1d40');
        // $this->response = $dadata->clean("phone", $value);
        return  $this->response;
    }
}
