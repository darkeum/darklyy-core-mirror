<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Valve Calculation Api Action 
*/

namespace Home\Actions;


use Sales\Models\Sales;
use Boot\Support\Carbon;
use Request\Model\Request;
use Boot\Support\Facades\Auth;
use Changelog\Models\Changelog;
use Home\Tasks\HomeGetNewsTask;
use Promotions\Models\Promotions;
use Boot\Abstracts\Actions\Action;


class ApiHomeAction extends Action
{
    /**
     * Обработчик действия
     *
     * @param  mixed $request
     * @return array
     */
    public function run(): array
    {
        $date = Carbon::now()->toDateString();
        $uid = Auth::id();
        $request_count = Request::where('uid_dir', $uid)->count();
        $sales_count = Sales::where('uid_send', $uid)->count();
        $changeLog = Changelog::where('category', 2)->get()
            ->append('textClear')
            ->append('dateClear')
            ->append('typeColor');

        $promotions = Promotions::where('status', 1)
            ->where('date_start', '<=', $date)
            ->where('date_end', '>=', $date)
            ->orderBy('id', 'desc')->get();
        $result = [];
        $result['data'] = [
            'request_count' => $request_count,
            'sales_count' => $sales_count,
            'changeLog' => $changeLog,
            'version' => [
                'numb' => '0.3.26 (alfa)',
                'date' => '28.05.2021',
            ],
            'news' => app(HomeGetNewsTask::class)->run(),
            'promotions' => $promotions,
        ];

        return $result;
    }
}
