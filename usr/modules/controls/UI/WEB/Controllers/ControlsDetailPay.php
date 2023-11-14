<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (05.04.2023 6:35)
*/
namespace Controls\UI\WEB\Controllers;

use Boot\System\Database\DB;
use Boot\System\Http\Request;
use Controls\Models\Control;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ControlsDetailPay {
    const s_status = 20;

    /**
     * Добавление даты частичной оплаты
     *
     * @param int $id
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function saveDate(int $id): array
    {
        $newDate = \request()->get('newDate');
        $uid = \auth()->id();
        $result = false;
        if($newDate) {
            $result = DB::table('control_payment')->insertGetId([
                'control_id' => $id,
                'date' => $newDate,
                'uid' => $uid,
            ]);
        }

        $messages = ($result)
            ? trans('controls::lang.status.date_update')
            : trans('controls::lang.status.error');

        return ['messages' => $messages, 'result' => $result, 'id' => $result];
    }

    /**
     * Удаление даты частичной оплаты
     *
     * @param int $id
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
     function deletePayDate(int $id): array
     {
        $result = false;
        if(\request()->get('delete')) {
            $result =  DB::table('control_payment')->delete($id);
        }

         $messages = ($result)
             ? trans('controls::lang.status.date_update')
             : trans('controls::lang.status.error');

         return ['messages' => $messages, 'result' => $result, 'id' => $id];
    }

    /**
     * Обновление даты счёта
     *
     * @param int $id
     * @param Request $request
     * @return void
     */
    function datePay(int $id, Request $request): void
    {
        $pay_date = $request->get('pay_date');
        if(!$pay_date) return;

        $control = Control::find($id);
        $control->pay_date = $pay_date;
        $control->save();
    }

}