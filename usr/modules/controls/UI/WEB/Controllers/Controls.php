<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (15.03.2023 7:38)
*/

namespace Controls\UI\WEB\Controllers;

use Boot\Controller\AbstractController;
use Boot\Models\CommentsAdmin;
use Boot\System\Http\Request;
use Controls\Models\Control;
use Controls\Support\Manager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class Controls extends AbstractController {

    static function index(): View
    {
        return view('controls::manager.index');
    }

    static function getControlData($id): array
    {
        $control = Control::with([
            'supply_order',
            'control_detail.supply_order_detail',
            'control_detail.sales_detail.sales.object_'
        ])->where('id', $id)->first();

        if(!$control) return [];

        $supply_order_total = 0;
        $sales_total = null;
        $control_detail = $control->control_detail;
        foreach ($control_detail as $key => $item) {
            $item->ndr = get_ndr(['cid' => $item->cid, 'stock_id' => '1', 'returnType' => 'view']);

            $supply_order_total += $item->supply_order_detail->total ?? 0;
            if(!empty($item->sales_detail)) {
                $sales_total += $item->sales_detail->total;
            }
        }

        $control->status_text = match ($control->status) {
            1 => '<span class="badge bg-warning rounded-pill">' . $control->status_view . '</span>',
            2 => '<span class="badge bg-danger rounded-pill">' . $control->status_view . '</span>',
            3 => '<span class="badge bg-success rounded-pill">' . $control->status_view . '</span>',
        };

        $control->pay_text = match($control->pay) {
            0, 3 => '<span class="badge bg-warning rounded-pill">' . $control->pay_view . '</span>',
            1 => '<span class="badge bg-success rounded-pill">' . $control->pay_view . '</span>',
            2 => '<span class="badge bg-danger rounded-pill">' . $control->pay_view . '</span>',
        };

        return [
            'control' => $control,
            'supply_order_total' => $supply_order_total,
            'sales_total' => $sales_total,
        ];
    }

    private static function getSalesData($control): Collection
    {
        $sales = cc();
        foreach($control->control_detail as $key => $item){
            if(!empty($item->sales_detail) && !$sales->contains('id', $item->sales_detail->sales->id)) $sales->push($item->sales_detail->sales);
        }
        return $sales;
    }

    private static function getComments($control): \Illuminate\Database\Eloquent\Collection|array
    {
        return CommentsAdmin::with('user')
            ->where('module', 'supply_order_control')
            ->where('module_id', $control->supply_o)
            ->get();
    }

    static function get_buttons_path(): string
    {
        return '@controls/manager/show/control_buttons';
    }

    static function show($id): Response|View
    {
        $data = self::getControlData($id);
        if(!$data) return \response('Произошла ошибка', 404);

        $control = $data['control'];

        $temp_data = [
            'control' => $control,
            'comments' => self::getComments($control),
            'sales' => self::getSalesData($control),
            'attach' => Manager::getAttach($control),
            'supply_order_total' => $data['supply_order_total'],
            'sales_total' => $data['sales_total'],
            'buttons_path' => self::get_buttons_path(),
        ];

        return view('controls::manager.main.show.index', $temp_data);
    }

    static function edit($id){
        
    }

    static function update(Request $request, $id)
    {
        $control = Control::findOrFail($id);
        $control->update($request->all());
        return $control->toJson();
    }

    static function destroy($id)
    {
        
    }

    /**
     * @param $id
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    static function end($id, Request $request): Response|Application|ResponseFactory
    {
        $status = $request->get('status');
        $comment = $request->get('comment');
        $content = [];
        if(isset($id, $status)){
            $content = \Control\Support\Control::end($id, $status, $comment);
        }
        return response($content);
    }

    /**
     * Обновление даты оплаты
     *
     * @param int $id
     * @param Request $request
     * @return void
     */
    static function updateDatePay(int $id, Request $request): void
    {
        $planPayDate = $request->get('plan_pay_date');
        if(!$planPayDate) return;

        $control = Control::find($id);
        $control->plan_pay_date = $planPayDate;
        $control->save();
    }
}