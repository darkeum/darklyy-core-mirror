<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (29.03.2023 5:54)
*/
namespace Controls\UI\WEB\Controllers;

use Boot\Abstracts\Controllers\Controller;
use Controls\Models\ControlStorage;
use Controls\Models\ControlStorageDetail;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ControlsStorage extends Controller {

    /**
     * @return View
     */
    function index(): View {
        return view('controls::manager.index');
    }

    /**
     * @param $id
     * @param ControlStorage $model
     * @return Factory|View|Response|Application|ResponseFactory|null
     */
     function show($id, ControlStorage $model): Factory|View|Response|Application|ResponseFactory|null
     {
         $topContent = $model::with([
             'control_storage_detail.storage_detail',
         ])->findOrFail($id);

         if(!$topContent) return response(trans('controls::lang.status.error'), 404);

         $content = ControlStorageDetail::with([
             'storage_detail.storage',
             'storage_detail.catalog'
         ])->where('control_storage_id', $id)->get();

         if(!$content) return response(trans('controls::lang.status.error'), 404);

         $content->each(function($item) {
             $getNdr = get_ndr([
                 'cid' => $item->storage_detail->cid,
                 'stock_id' => $item->storage_detail->storage->stock_id,
                 'module' => 'sales',
                 'pid' => null,
                 'returnType' => 'array',
             ]);
             $item->getNdr = $getNdr['n'] . ' / ' . $getNdr['d'] . ' / ' . $getNdr['r'];
             $item->getUnit = get_unit('data', $item->storage_detail->catalog->unit);
         });

        return view('controls::manager.storage.detail.index', [
            'status' => $this->getStatus(),
            'content' => $content,
            'topContent' => $topContent,
        ]);
    }

    /**
     * @return array
     */
    private function getStatus(): array
    {
        return trans('controls::lang.statusStorage');
    }

}