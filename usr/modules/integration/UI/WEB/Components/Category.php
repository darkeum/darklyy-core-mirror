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

use Boot\Support\Str;
use Boot\Models\Categories;
use Boot\Support\Facades\Auth;
use Boot\Support\Facades\Http;
use Boot\System\Livewire\Component;
use Integration\Models\IntegrationCategory;

class Category extends Component
{
    public $categories;
    public $integration_type;
    public $integration_types;
    public array $category_id;
    public $integration_category;
    public $category_integration_id;
    public $category_integration_title;
    public $integration;

    public function mount()
    {
        $this->category_id = [];
        $this->integration_type = 'null';
        $this->category_integration_id = null;
        $this->category_integration_title = null;
        $this->integration = IntegrationCategory::with('categories')->get()->toArray();
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
        $this->categories = Categories::select("id", "name")->where('module', 'catalog_v2')->get();
    }

    public function ozon_categories()
    {
        $response = Http::withHeaders([
            'Client-Id' => '365843',
            'Api-Key' => '548de3af-c542-4831-99bf-a51264203750'
        ])->post('https://api-seller.ozon.ru/v2/category/tree', [
            'language' => 'DEFAULT',
        ])->json();
        $pre = json_encode($response['result'], JSON_UNESCAPED_UNICODE);
        $pre = Str::replace($pre, 'title', 'text');
        $pre = Str::replace($pre, 'category_id', 'id');
        return $pre;
    }

    public function change_integration_type()
    {
        $this->integration_category($this->integration_type);
        $this->emit('integrationTypeChanged');
    }

    public function integration_category($type = null)
    {
        switch ($type) {
            case 'ozon':
                $this->integration_category = $this->ozon_categories();
                break;

            default:
                $this->integration_category = null;
                break;
        }
    }

    public function save()
    {
        foreach ($this->category_id as $key => $value) {
            $ic = IntegrationCategory::where('category_id', $value)->where('type_integration', $this->integration_type)->first();
            $res = [
                'category_id' => $value,
                'type_integration' => $this->integration_type,
                'category_integration_id' => $this->category_integration_id,
                'category_integration_title' => $this->category_integration_title,
                'uid' => Auth::id()
            ];
            if (!empty($ic)) {
                $ic->update($res);
            } else {
                IntegrationCategory::create($res);
            }
        }
        $this->integration = IntegrationCategory::with('categories')->get()->toArray();
        $this->emit('integrationSaved');
    }

    public function delete($id)
    {
        IntegrationCategory::find($id)->delete();
        $this->integration = IntegrationCategory::with('categories')->get()->toArray();
        $this->emit('integrationDeleted');
    }

    public function render()
    {
        return view('integration::manager.components.category');
    }
}
