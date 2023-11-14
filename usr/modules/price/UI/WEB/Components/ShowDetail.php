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

use Boot\System\Livewire\Component;
use Price\Models\CatalogPriceDetail;

class ShowDetail extends Component
{
    public $price_id;
    public $catalog_price;
    public $currency;
    public $perPage = 500;

    protected $listeners = [
        'itemAdd' => 'refresh',
        'refresh' => 'refresh'
    ];

    public function render()
    {
        return view('price::manager.components.show.detail', [
            'items' => CatalogPriceDetail::where('price_id', $this->price_id)->with('catalog')->with('catalog.catalog_unit')->paginate($this->perPage)
        ]);
    }

    public function loadMore()
    {
        $this->perPage += 500;
    }

    public function refresh()
    {
        $this->perPage = 500;
        $this->render();
    }
}
