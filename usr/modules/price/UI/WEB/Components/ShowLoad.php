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

use Catalog\Models\Catalog;
use Boot\System\Livewire\Component;
use Price\Models\CatalogPriceDetail;
use Boot\System\Livewire\WithFileUploads;
use Boot\System\Office\Spreadsheet\IOFactory;

class ShowLoad extends Component
{
    use WithFileUploads;

    public $price_id;
    public $cid;
    public $price;
    public $start;
    public $file;

    public function render()
    {
        return view('price::manager.components.show.load');
    }

    public function mount()
    {
        $this->cid = 'A';
        $this->price = 'B';
        $this->start = '2';
    }

    public function load()
    {
        $this->validate([
            'file' => 'required|mimes:xls,xlsx|max:10240',
        ]);
        $this->emit('modalBlock');
        $excel = IOFactory::load($this->file->getPath() . DIRECTORY_SEPARATOR . $this->file->getFilename());
        $end = $excel->setActiveSheetIndex(0)->getHighestRow();

        for ($i = $this->start; $i <= $end; $i++) {
            $cid = null;
            if (!empty($this->cid)) {
                $cid = $excel->getActiveSheet()->getCell($this->cid . $i)->getValue();
            }
            $sku = null;
            if (!empty($this->sku)) {
                $sku = $excel->getActiveSheet()->getCell($this->sku . $i)->getValue();
            }
            $price = $excel->getActiveSheet()->getCell($this->price . $i)->getOldCalculatedValue();
            if (empty($price)) {
                $price = $excel->getActiveSheet()->getCell($this->price . $i)->getCalculatedValue();
            }
            if (!is_numeric($price)) {
                $price = $excel->getActiveSheet()->getCell($this->price . $i)->getValue();
            }
            $price = str_replace(",", ".", $price);

            if (!is_numeric($price)) {
                continue;
            }

            if (!empty($cid) || !empty($sku)) {
                if (!empty($cid)) {
                    if (!empty(Catalog::find($cid))) {
                        CatalogPriceDetail::create(["cid" => $cid, "price_id" => $this->price_id, "sum" => $price]);
                    }
                }
            }
        }

        $this->emit('itemAdd');
        $this->emit('modalClose');
    }

}
