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

use Boot\Models\Attach;
use Boot\Support\Facades\Request;
use Support\Models\Support;
use Boot\Support\Facades\Auth;
use Support\Models\SupportMessage;
use Boot\System\Livewire\Component;
use Boot\System\Livewire\WithFileUploads;
use Support\Notifications\SupportNewMessage;
use Boot\System\Media\Http\Livewire\Concerns\WithMedia;

class Chat_Form extends Component
{
    use WithMedia, WithFileUploads;

    public $support;
    public $comment;
    public $attaches;
    public $fileChange = false;
    public $mediaComponentNames = ['attaches'];

    protected $listeners = ['refreshForm' => 'render'];

    public function render()
    {
        return view('support::manager.components.chat_form');
    }

    protected function rules()
    {
        return [
            'comment' => 'required',
            //'attaches' => 'image|max:1024', // 1MB Max
        ];
    }

    public function submit()
    {
        if (empty($this->attaches)) {
            $this->validate();
        } else {
            $this->comment = (!empty($this->attaches) and !empty($this->comment))
                ? $this->comment :
                '';
                //$this->attaches->getClientOriginalName();
        }

        $support_message_request = [
            'support_id' => $this->support->id,
            'uid' => Auth::id(),
            'message' => $this->comment,
        ];

        $arr = [
            //'getRealPath' => $this->attaches->getRealPath(),
            //'getClientOriginalName' => $this->attaches->getClientOriginalName(),
            //'getClientOriginalExtension' => $this->attaches->getClientOriginalExtension(),
            //'getSize' => $this->attaches->getSize(),
            //'getMimeType' => $this->attaches->getMimeType(),
            //
            //'extension' => $this->attaches->extension(),
            //'path' => $this->attaches->path(),
        ];
        //dd($arr);

        $support_message = SupportMessage::create($support_message_request);

        // Грузим файл в медиа
        if (!empty($this->attaches)) {

            $media = $support_message
                ->addMedia($this->attaches->path())
                ->usingName($this->attaches->getClientOriginalName())
                ->usingFileName($this->attaches->getClientOriginalName())
                ->toMediaCollection('support');

            $support_message->update([
                'media_id' => $media->id
            ]);
        }

        // Уведомления
        $this->support = Support::find($this->support->id);
        if ($this->support->uid != $this->support->uid_dir) {
            if ($this->support->uid != Auth::id()) {
                $this->support->user->notify(new SupportNewMessage($this->support));
            }
            if (!empty($this->support->uid_dir)) {
                if ($this->support->uid_dir != Auth::id()) {
                    $this->support->user_dir->notify(new SupportNewMessage($this->support));
                }
            }
        }

        $this->comment = null;
        $this->attaches = null;
        $this->fileChange = false;
        //$this->clearMedia('attaches');
    }

    public function change()
    {
        $this->dispatchBrowserEvent('change');
        $this->fileChange = true;
    }
}
