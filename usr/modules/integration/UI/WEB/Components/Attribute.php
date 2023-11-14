<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Integration\UI\WEB\Components;

use Boot\Support\Facades\Auth;
use Boot\Support\Facades\Http;
use Boot\System\Livewire\Component;
use Catalog\Models\CatalogAttribute;
use Integration\Models\IntegrationAttribute;
use Integration\Models\IntegrationCategory;

class Attribute extends Component
{
    public $categories;
    public $integration_type;
    public $integration_types;
    public $category_id;
    public $integration_attribute;
    public $category_integration_id;
    public $category_integration_title;
    public $integration;
    public $integration_attribute_temp;
    public $attribute;

    public function mount()
    {
        $this->category_id = 'null';
        $this->categories = [];
        $this->attribute = [];
        $this->integration_attribute_temp = [];
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

    public function get_attribute()
    {
        if (!empty($this->category_id)) {
            $category_id = $this->category_id;
            $this->attribute = CatalogAttribute::with(["attribute_category_one" => function ($query) use ($category_id) {
                $query->where("category_id", $category_id);
            }])->whereHas("attribute_category", function ($query) use ($category_id) {
                $query->where("category_id", $category_id)->orderBy("position")->orderBy("id");
            })->get()->toArray();
        }
    }

    public function ozon_attribute($category_integration_id)
    {
        $response = Http::withHeaders([
            'Client-Id' => '365843',
            'Api-Key' => '548de3af-c542-4831-99bf-a51264203750'
        ])->post('https://api-seller.ozon.ru/v3/category/attribute', [
            'attribute_type' => 'ALL',
            'category_id' => [$category_integration_id],
            'language' => 'DEFAULT',
        ])->json();
        return $response['result'][0]['attributes'];
    }

    public function change_integration_type()
    {
        $this->categories = IntegrationCategory::where('type_integration', $this->integration_type)->with('categories')->get()->toArray();
        $this->emit('integrationTypeChanged');
    }

    public function change_category_id()
    {
        $this->integration_attribute_temp = [];
        $this->get_integration();
        $this->integration_attribute($this->integration_type, $this->category_id);
        $this->get_attribute();
    }

    public function integration_attribute($type, $category_id)
    {

        $ic = IntegrationCategory::where('type_integration', $this->integration_type)->where('category_id', $category_id)->first();
        switch ($type) {
            case 'ozon':
                $this->integration_attribute = $this->ozon_attribute($ic->category_integration_id);
                break;

            default:
                $this->integration_attribute = [];
                break;
        }
    }

    public function save()
    {
        if (!empty($this->integration_attribute_temp)) {
            $ic = IntegrationCategory::where('type_integration', $this->integration_type)->where('category_id', $this->category_id)->first();
            foreach ($this->integration_attribute_temp as $key => $value) {
                $ia = IntegrationAttribute::where('integration_category_id', $ic->id)->where('attribute_id', $key)->first();
                $ia_data = [];
                foreach ($this->integration_attribute as $ia_key => $ia_value) {
                    if ($ia_value['id'] == $value) {
                        $ia_data = $ia_value;
                        break;
                    }
                }
                $res = [
                    'integration_category_id' => $ic->id,
                    'attribute_id' => $key,
                    'attribute_integration_id' => $value,
                    'attribute_integration_name' => $ia_data['name'],
                    'attribute_integration_type' => $ia_data['type'],
                    'attribute_integration_dictionary' => ($ia_data['dictionary_id'] == 0) ? false : true,
                    'uid' => Auth::id()
                ];
                if (!empty($ia)) {
                    $ia->update($res);
                } else {
                    IntegrationAttribute::create($res);
                }
            }
        }
        $this->get_integration();
        $this->emit('integrationSaved');
    }

    public function delete($id)
    {
        IntegrationAttribute::find($id)->delete();
        $this->get_integration();
        $this->emit('integrationDeleted');
    }

    public function get_integration(){
        $ic = IntegrationCategory::where('type_integration', $this->integration_type)->where('category_id', $this->category_id)->first();
        $this->integration = IntegrationAttribute::with('attribute')->where('integration_category_id', $ic->id)->get()->toArray();
    }

    public function render()
    {
        return view('integration::manager.components.attribute');
    }
}
