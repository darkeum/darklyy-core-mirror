<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (14.07.2023 7:28)
*/

namespace Controls\UI\WEB\Components\Storage\Detail;

use Boot\System\Livewire\Component;
use Controls\Models\ControlStorage;
use Controls\Models\ControlStorageDetail;
use Illuminate\View\View;

class TableStorage extends Component {

    public object $content;
    public array $status;
    public int $id_detail;
    protected $listeners = ['update'];

    function render(): View
    {
        return view('controls::manager.storage.detail.components.table');
    }

    function update($params, ControlStorageDetail $controlStorageDetail, ControlStorage $controlStorage): void {
        $json = json_decode($params['json'], true);

        $ids = array_keys($json);
        foreach ($controlStorageDetail::find($ids) as $item) {
            $storage_detail = $item->storage_detail;
            $storage = $storage_detail->storage;

            $status = $json[ $item->id ];
            if($status == 2 && $item->status == 1){
                $source = [];
                $source['module'] = 'storage';
                $source['pid'] = $item->storage_id;
                $source['comment'] = 'Корректировка остатков #'.module_perfix('storage', $item->storage_id);
                $action = $storage_detail->count_now > 0 ? "plus" : "minus";
                updateQuantity($storage_detail->cid, $storage->stock_id, abs($storage_detail->count_now), $action, $source);
            }

            $item->status = $status;
            $item->save();
        }
        if($controlStorageDetail::where('control_storage_id', $this->id_detail)->where('status', 1)->count() == 0){
            $control_storage = $controlStorage::find($this->id_detail);
            $control_storage->status = 2;
            $control_storage->save();
        }

        $this->content = $controlStorageDetail::with([
            'storage_detail.storage',
            'storage_detail.catalog'
        ])->where('control_storage_id', $this->id_detail)->get();
    }
}