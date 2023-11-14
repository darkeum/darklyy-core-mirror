<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\UI\WEB\Components;

use Boot\Support\Facades\DB;
use Boot\System\Livewire\Component;
use Price\Models\CatalogPriceType;

class Search extends Component
{

    public $cid;
    public $items;
    public $price_type;
    public $search_flag;
    public $catalog_price_type;

    protected function messages()
    {
        return ['cid.required' => __('price::search.cid.required')];
    }


    public function render()
    {
        return view('price::manager.components.search');
    }

    protected function rules()
    {
        return [
            'cid' => 'required',
        ];
    }

    public function mount()
    {
        $this->catalog_price_type = CatalogPriceType::all();
        $this->items = [];
        $this->price_type = [];
        $this->search_flag = false;
    }

    public function search()
    {
        $this->validate();
        $this->search_flag = true;
        $this->items = [];
        $query = DB::table('catalog_price_detail')
            ->select(['catalog_price_detail.sum', 'catalog_price.id', 'catalog_price_type.title', 'catalog_price.date', 'catalog_price.active', 'catalog_price.price_type', 'catalog_price.uid', 'catalog_price.module', 'catalog_price.pid', 'catalog_price_type.currency'])
            ->leftJoin('catalog_price', 'catalog_price_detail.price_id', '=', 'catalog_price.id')
            ->leftJoin('catalog_price_type', 'catalog_price.price_type', '=', 'catalog_price_type.id')
            ->where('catalog_price_detail.cid', '=', $this->cid);
        if (count($this->price_type) > 0) {
            $query->whereIn('catalog_price.price_type', $this->price_type);
        }
        $items = $query->orderBy('catalog_price.date', 'DESC')->orderBy('catalog_price_detail.id', 'DESC')->get()->toArray();
        foreach ($items as $key => $value) {
            $this->items[$key] = (array) $value;
        }
    }
}
