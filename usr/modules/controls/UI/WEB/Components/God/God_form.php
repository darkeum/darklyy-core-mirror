<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (16.08.2023 16:57)
*/

namespace Controls\UI\WEB\Components\God;

use Boot\Models\Users;
use Boot\System\Livewire\Component;
use Boot\System\Validation\Exceptions\ValidationException;
use Illuminate\View\View;
use Stocks\Models\StockMinus;

class God_form extends Component
{
    public array $moduleName;

    public $data;

    protected $rules = [
        'data.pid' => 'required|integer',
        'data.module' => 'required',
        'data.uid' => 'required',
    ];

    protected $messages = [
        'data.pid.required' => 'Поле уникальный идентификатор должно быть обязательным',
        'data.pid.integer' => 'Поле уникальный идентификатор должно быть числовым',
        'data.module.required' => 'Поле модуль должно быть обязательным',
        'data.uid.required' => 'Поле пользователь должно быть обязательным',
    ];

    function render(): View
    {
        return view('controls::manager.god.components.form', [
            'users' =>Users::query()
                        ->where('group', 1)
                        ->where('active', 1)
                        ->orderBy('id')
                        ->get(),
            ]);
    }

    /**
     * @throws ValidationException
     */
    function save(): void
    {
        $data = $this->validate();

        $result = StockMinus::create($data['data']);
        if ($result) {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'success',
                'message' => 'Успешно!',
            ]);
        } else {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'error',
                'message' => 'Произошла ошибка!',
            ]);
        }
    }
}