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

use Boot\Support\Currency;
use Price\Models\CatalogPrice;
use Boot\System\Livewire\Component;
use Price\UI\CLI\CatalogPriceDaemon;

class ShowBody extends Component
{
    public $price_id;
    public $catalog_price;
    public $currency;

    protected $listeners = ['refresh'];

    public function render()
    {
        return view('price::manager.components.show.body');
    }

    public function mount()
    {
        $this->catalog_price = $this->getInfo();
        $this->currency = Currency::get($this->catalog_price->type->currency)->short();
    }

    public function refresh()
    {
        $this->catalog_price = $this->getInfo();
    }

    public function getInfo()
    {
        return CatalogPrice::where('id', $this->price_id)->with('type')->with('attach')->first();
    }

    public function reactive()
    {
        $price = CatalogPrice::find($this->price_id);
        $price->update(['active' => !$this->catalog_price->active]);
        if(!$this->catalog_price->active == 1){
            $price->update(['daemon' => 0]);
            CatalogPriceDaemon::key_create();
        }
        $this->catalog_price = $this->getInfo();
    }
}
