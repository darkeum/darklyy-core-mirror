<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (16.08.2023 16:04)
*/
namespace Controls\UI\WEB\Controllers;

use Boot\Controller\AbstractController;
use Boot\Support\Facades\Config;
use Boot\System\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Stocks\Models\StockMinus;

class ControlsGod extends AbstractController {

    /**
     * @return View
     */
    function index(): View
    {
        return view('controls::manager.god.index', [
            'moduleName' => $this->getModuleName(),
            'language' => $this->getLanguage(),
        ]);
    }

    /**
     * @return array[]
     */
    private function getModuleName(): array
    {
        return Config::get('god-config');
    }

    /**
     * @param Request $request
     * @return string
     */
    public function search(Request $request): string
    {
        $module = $request->get('module');

        $result = $this->getData();

        if($module) {
            $result = $result->where('module', '===', $module);
        }

        return $this->prepareData($result);
    }

    /**
     * Удаляем данные из таблицы
     *
     * @param Request $request
     * @return bool
     */
    public function delete(Request $request): bool
    {
        $id = (int) $request->get('id');
        if(!$id) return false;

        return (bool) StockMinus::destroy($id);
    }

    /**
     * Получение значений по списку разрешений
     * @return Collection
     */
    private function getData(): Collection
    {
        return StockMinus::query()
            ->select('stock_minus.*', 'stock_minus.id as stockId', 'users.*' )
            ->join('users', 'stock_minus.uid', '=', 'users.id')
            ->get();
    }

    /**
     * Подготавливаем данные для отображение в таблице
     * @param Collection $data
     * @return string
     */
    private function prepareData(Collection $data): string
    {
        $moduleName = $this->getModuleName();
        return  $data->transform(function($item) use ($moduleName) {
            return [
                'module' => $moduleName[$item->module]['ru'],
                'pid' => $item->pid,
                'name' => user_short_name($item),
                'action' => view('controls::manager.god.action',
                    [
                        'href' => $moduleName[$item->module]['link'] . $item->pid,
                        'route' => route('admin.controls.delete_list'),
                        'id' => $item->stockId,
                    ])->render(),
            ];
        })->toJson();
    }

    // Языковой файл для Grid
    protected function getLanguage(): array
    {
        $language = trans('grid::lang');
        $language['loadingRecords'] = str(view('elements.preloader', ['bg' => false])->render())->replace(["\r\n", "\r", "\n", "\t", '  '], '');
        return $language;
    }
}
