<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan && Kirill Gusev
*/

namespace Integration\UI\WEB\Components;

use Boot\Support\Facades\Auth;
use Boot\Support\Facades\Http;
use Boot\System\Livewire\Component;
use Catalog\Models\CatalogAttribute;
use Integration\Models\IntegrationAttribute;
use Integration\Models\IntegrationCategory;
use Integration\Models\IntegrationValues;

class Values extends Component
{
    public $categories;
    public $integration_type;
    public $integration_types;
    public $category_id;
    public $attribute_id;
    public $integration_attribute;
    public $category_integration_id;
    public $category_integration_title;
    public $integration;
    public $integration_values_temp;
    public $attribute_values;

    public $attributes;

    public function mount()
    {

        $this->category_id = 'null';
        $this->attribute_id = 'null';
        $this->categories = [];
        $this->attribute_values = [];
        $this->attributes = [];
        $this->integration_values_temp = [];
        $this->integration_type = 'null';
        $this->category_integration_id = null;
        $this->category_integration_title = null;
        $this->integration_types = [
            [
                'id' => 'ozon',
                'title' => 'OZON Seller',
                'disabled' => false
            ],
            [
                'id' => 'yandex',
                'title' => 'Яндекс Маркет',
                'disabled' => true
            ]
        ];
    }

    public function get_attribute_values()
    {
        if (!empty($this->category_id) && !empty($this->attribute_id)) {
            $this->attribute_values = CatalogAttribute::with(["attribute_value" => function ($query) {
                $query->orderBy("value")->orderBy("id");
            }])->where("id", $this->attribute_id)->first()->toArray();

            if(empty($this->attribute_values))
            {
                $this->emit('notification',"Нет характеристик в нашей базе");
            }
        }
    }

    public function ozon_value($category_integration_id, $attribute_id)
    {
        $aat = IntegrationAttribute::find($attribute_id);

        // dd([
        //     'attribute_id' => $aat->attribute_integration_id,
        //     'category_id' => $category_integration_id,
        //     'language' => 'DEFAULT',
        //     'last_value_id' => '0',
        //     'limit' => '5000',


        // ]);
        $response = Http::withHeaders([
            'Client-Id' => '365843',
            'Api-Key' => '548de3af-c542-4831-99bf-a51264203750'
        ])->post('https://api-seller.ozon.ru/v1/description-category/attribute/values', [
            'attribute_id' => $aat->attribute_integration_id,
            'category_id' => $category_integration_id,
            'language' => 'DEFAULT',
            'last_value_id' => '0',
            'limit' => '5000',
        ])->json();

        return $response['result'] ?? [];
    }

    public function change_integration_type()
    {
        $this->categories = IntegrationCategory::where('type_integration', $this->integration_type)->with('categories')->get()->toArray();
        $this->emit('integrationTypeChanged');
    }

    public function change_category_id()
    {
        $this->integration_values_temp = [];
        $this->integration= [];
        $this->attribute_values = [];

        $this->get_integration_attribute();
    }

    public function change_attribute_id()
    {
        $this->integration_values_temp = [];


        $this->get_attribute_values();
        $this->integration_attribute();
        $this->get_integration();
        // dd($this->attribute_values);
    }

    public function integration_attribute()
    {

        $ic = IntegrationCategory::where('type_integration', $this->integration_type)->where('category_id', $this->category_id)->first();
        switch ($this->integration_type) {
            case 'ozon':
                $this->integration_attribute = $this->ozon_value($ic->category_integration_id, $this->attribute_id);
                break;

            default:
                $this->integration_attribute = [];
                break;
        }

        if(empty($this->integration_attribute))
        {
            $this->emit('notification',"Нет характеристик у интегратора");
        }
        // dd($this->integration_attribute);
    }

    public function save()
    {
        if (!empty($this->integration_values_temp)) {

            $ic = IntegrationAttribute::find($this->attribute_id);
            foreach ($this->integration_values_temp as $key => $value) {
                $ia = IntegrationValues::where('integration_attribute_id', $ic->id)->where('value_id', $key)->first();
                $ia_data = [];
                foreach ($this->integration_attribute as $ia_key => $ia_value) {
                    if ($ia_value['id'] == $value) {
                        $ia_data = $ia_value;
                        break;
                    }
                }

                $res = [
                    'integration_attribute_id' => $ic->id,
                    'value_id' => $key,
                    'value_integration_id' => $value,
                    'value_integration_value' => $ia_data['value'],
                    'uid' => Auth::id()
                ];
                if (!empty($ia)) {
                    $ia->update($res);
                } else {
                    IntegrationValues::create($res);
                }
            }
        }
        $this->get_integration();
        $this->emit('integrationSaved');
    }

    public function delete($id)
    {
        IntegrationValues::find($id)->delete();
        $this->get_integration();
        $this->emit('integrationDeleted');
    }

    public function get_integration()
    {
        $ic = IntegrationAttribute::find($this->attribute_id);
        $this->integration = IntegrationValues::with('values')->where('integration_attribute_id', $ic->id)->get()->toArray();
    }

    public function get_integration_attribute()
    {
        $ic = IntegrationCategory::where('type_integration', $this->integration_type)->where('category_id', $this->category_id)->first();
        $this->attributes = IntegrationAttribute::with('attribute')->where('integration_category_id', $ic->id)->get()->toArray();
    }


    public function render()
    {
        return view('integration::manager.components.values');
    }
}
