<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Warranties\UI\WEB\Components;

use Realization\Model\Realization;
use Boot\System\Livewire\Component;
use Realization\Model\RealizationDetail;
use Boot\System\Office\Word\TemplateProcessor;

class WarrantySearch extends Component
{
    public $name, $sku, $serial_number, $org_name, $date, $fio, $realization_id;

    protected $listeners = ['updateInfo', 'get_data'];

    public function render()
    {
        return view('warranties::manager.components.searchForm');
    }

    public function get_data($id)
    {
        $this->resetModels();
        $this->realization_id = $id;

        $result = Realization::with(["realization_detail" => function ($query) {
            $query->whereHas("catalog", function ($query) {
                $query->where("man", 20);
            });
        }, "user_create", "org_.organization"])->where("id", $id)->first();

        $this->org_name = $result->org_->organization->name ?? null;
        $this->date = $result->date ?? null;
        $this->fio = $result->user_create->short_user_name ?? null;
        $this->refreshEvent();
    }

    public function updateInfo($id)
    {
        $unit = RealizationDetail::find($id);
        $this->name = $unit->s_name;
        $this->sku = $unit->s_sku;
        $this->refreshEvent();
    }

    public function getPrint()
    {
        $data = [
            'org_name'      => $this->org_name,
            'date'          => $this->date,
            'fio'           => $this->fio,
            'name'          => $this->name,
            'sku'           => $this->sku,
            'serial_number' => $this->serial_number,
        ];
        $document =  new TemplateProcessor(module_path('Resources' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'wilo.docx'));
        foreach ($data as $k => $v) {
            switch ($k) {
                default:
                    $document->setValue($k, $v);
                    break;
                case "date":
                    $document->setValue($k, date("d.m.Y", strtotime($v)));
                    break;
            }
        }
        $file_url = module_path('Resources' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . uniqid() . '.docx');
        $document->saveAs($file_url);
        return response()->download($file_url)->deleteFileAfterSend(true);
    }

    public function refreshEvent()
    {
        $this->dispatchBrowserEvent('flapickerRefresh');
    }

    public function resetModels()
    {
        $this->realization_id = null;
        $this->org_name =  null;
        $this->date =  null;
        $this->fio =  null;
        $this->name = null;
        $this->sku = null;
        $this->serial_number = null;

        $this->refreshEvent();
    }

    public function UpdatedName()
    {
        $this->refreshEvent();
    }

    public function UpdatedRealizationId()
    {
        $this->refreshEvent();
    }

    public function UpdatedOrgName()
    {      
        $this->refreshEvent();
    }

    public function UpdatedDate()
    {
        $this->refreshEvent();
    }

    public function UpdatedFio()
    {
        $this->refreshEvent();
    }

    public function UpdatedSerialNumber()
    {
        $this->refreshEvent();
    }
}
