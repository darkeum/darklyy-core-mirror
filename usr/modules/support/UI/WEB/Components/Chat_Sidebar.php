<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Support\UI\WEB\Components;

use Boot\Support\Carbon;
use Support\Models\Support;
use Support\Tasks\MarksTask;
use Support\Tasks\TypesTask;
use Boot\Support\Facades\Auth;
use Support\Models\SupportMessage;
use Boot\System\Livewire\Component;
use Support\Notifications\SupportStart;
use Support\Notifications\SupportCancel;
use Support\Notifications\SupportResume;
use Support\Notifications\SupportComplete;
use Support\Notifications\ChangePlannedDate;

class Chat_Sidebar extends Component
{
    public Support $support;
    public $support_id;
    public $types;
    public $marks;
    public $planned;
    public $view;

    public $status;
    public $editable;

    //protected $listeners = ['checkForm' => 'complete'];

    public function mount()
    {
        $this->types = app(TypesTask::class)->run();
        $this->editable = false;
        $this->marks = app(MarksTask::class)->run();
        $this->support = Support::find($this->support_id);
        $this->status = $this->support->status ?? 1;
        $this->planned = empty($this->support->planned_at) ? null : Carbon::create($this->support->planned_at)->format("Y-m-d\TH:i");
        $this->view = empty($this->support->view_at) ? null : Carbon::create($this->support->view_at)->format("Y-m-d\TH:i");
    }

    public function render()
    {
        return view('support::manager.components.chat_sidebar');
    }

    protected $rules = [
        'support.title' => 'string',
        'support.type' => 'string|nullable',
        'support.place' => 'string|nullable',
        'support.url' => 'string|nullable',
        'support.mark' => 'numeric|nullable',
    ];

    // Обновление
    public function updated()
    {
        $this->validate();
    }

    // Сохранение
    public function save()
    {
        $this->validate();
        $this->editable = false;
        if ($this->support->planned_at != Carbon::parse($this->planned)->format('Y-m-d H:i:s')) {
            $this->support->planned_at = Carbon::parse($this->planned)->format('Y-m-d H:i:s');
        }
        if ($this->support->view_at != Carbon::parse($this->view)->format('Y-m-d H:i:s')) {
            $this->support->view_at = Carbon::parse($this->view)->format('Y-m-d H:i:s');
        }

        $dirty = $this->support->getDirty();

        $message = [];
        foreach ($dirty as $key => $value) {
            switch ($key) {
                case 'view_at':
                    $message[] = "Дата обнаружения изменена на: " . Carbon::parse($value)->format('d.m.Y H:i');
                    break;
                case 'title':
                    $message[] = "Изменен заголовок заявки: " . $value;
                    break;
                case 'type':
                    $message[] = "Изменен тип заявки на: " . app(TypesTask::class)->run($value, 'default');
                    break;
                case 'place':
                    $message[] = "Место обнаружения изменено на: " . $value;
                    break;
                case 'url':
                    $message[] = "URL обнаружения изменен на: " . $value;
                    break;
                case 'mark':
                    $message[] = "Приоритет заявки изменен на: " . app(MarksTask::class)->run($value, 'default');
                    break;
                case 'planned_at':
                    $support_message_request = [
                        'support_id' => $this->support->id,
                        'uid' => Auth::id(),
                        'message' => "Планируемая дата выполнения заявки изменена на: " . Carbon::parse($this->support->planned_at)->format('d.m.Y H:i'),
                    ];
                    SupportMessage::create($support_message_request);
                    //$this->support->user->notify(new ChangePlannedDate($this->support));
                    break;
            }
        }
        if (!empty($message)) {

            $support_message_request = [
                'support_id' => $this->support->id,
                'uid' => Auth::id(),
                'message' => implode("<br>", $message)
            ];

            SupportMessage::create($support_message_request);

        }

        $this->support->save();
    }

    // Редактирование
    public function edit()
    {
        $this->editable = true;
    }

    // Шаг назад
    public function back()
    {
        $this->editable = false;
    }

    // Заявка принята в работу
    public function start()
    {
        $this->status = 2;
        $this->support->uid_dir = Auth::id();
        $this->support->started_at = Carbon::now();
        $this->support->status = 2;
        $this->support->save();

        $support_message_request = [
            'support_id' => $this->support->id,
            'uid' => Auth::id(),
            'message' => "Заявка принята в работу",
        ];

        SupportMessage::create($support_message_request);
        $this->support->user->notify(new SupportStart($this->support));
    }

    // Заявка отклонена
    public function cancel()
    {
        $this->status = 4;
        $this->support->uid_dir = Auth::id();
        $this->support->started_at = Carbon::now();
        $this->support->completed_at = Carbon::now();
        $this->support->status = $this->status;
        $this->support->save();

        $support_message_request = [
            'support_id' => $this->support->id,
            'uid' => Auth::id(),
            'message' => "Заявка отклонена",
        ];

        SupportMessage::create($support_message_request);
        $this->support->user->notify(new SupportCancel($this->support));
    }

    // Заявка выполнена
    public function complete()
    {
        $this->status = 3;
        $this->support->completed_at = Carbon::now();
        $this->support->status = 3;
        $this->support->save();

        $support_message_request = [
            'support_id' => $this->support->id,
            'uid' => Auth::id(),
            'message' => "Заявка выполнена",
        ];

        $this->emit('refreshForm');

        SupportMessage::create($support_message_request);
        $this->support->user->notify(new SupportComplete($this->support));
    }

    // Заявка возвращена в работу
    public function resume()
    {
        $this->status = 2;
        $this->support->completed_at = null;
        $this->support->status = 2;
        $this->support->save();

        $support_message_request = [
            'support_id' => $this->support->id,
            'uid' => Auth::id(),
            'message' => 'Заявка возвращена в работу',
        ];

        $this->emit('refreshForm');

        SupportMessage::create($support_message_request);
        $this->support->user->notify(new SupportResume($this->support));
    }
}
