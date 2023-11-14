<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (28.09.2023 13:11)
*/

namespace EconomicNew\UI\WEB\Controllers;

use Boot\Controller\AbstractController;
use Boot\Support\Module;
use Boot\System\Http\Request;
use EconomicNew\Actions\EconomicActAction;
use EconomicNew\Models\ActReconciliation;
use EconomicNew\UI\WEB\Requests\EconomicActRequest;
use EconomicNew\UI\WEB\Traits\EconomicActTrait;
use Illuminate\Contracts\View\View;
use Organizations\Model\Org;
use Organizations\Model\Organizations;
use Boot\Models\Users;
class EconomicAct extends AbstractController
{

    public function index(): View
    {
        return view('economicNew::manager.act.index');
    }

    public function create(): View
    {
        return view('economicNew::manager.act.create', [
            'organizations' => Org::cursor(),
            'counterpartys' => Organizations::cursor(),
            'responsible' => Users::where('group', 1)->get(),
        ]);
    }

    /**
     * Добавление в таблицу act_reconciliation
     * @param EconomicActRequest $request
     * @param EconomicActAction $actAction
     * @return void
     */
    public function store(EconomicActRequest $request, EconomicActAction $actAction): void
    {

        $actAction->store($request->all());
//        $values = $this->prepareStoreValues($request);
//        $result = ActReconciliation::query()->create($values);
//        dump($result);
    }

    /**
     * Подготавливаем данные для добавления в таблицу 'act_reconciliation'
     * @param EconomicActRequest $request
     * @return array
     */
    private function prepareStoreValues(EconomicActRequest $request):array
    {
        $values = $request->input();
        if($request->input('numb')) $values['numb_counter'] = Module::prefixWithId('act_reconciliation', $request->numb, $request->org);

        $values['uid_created'] = auth()->id();
        $values['org_data'] = Org::query()->find($request->org)?->organization?->toJson();
        $values['oid_data'] = Organizations::query()->find($request->oid)?->toJson();
        $values['org_director'] = Org::query()->find($request->org)?->director_user;
        $values['records'] = null;
        dd($values);
        return $values;
    }
}