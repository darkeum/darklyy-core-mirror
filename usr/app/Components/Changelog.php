<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace App\Components;

use Boot\Support\Facades\Auth;
use Boot\System\Livewire\Component;
use Changelog\Models\Changelog as ModelsChangelog;
use Changelog\Models\ChangelogRead;

class Changelog extends Component
{
    public $count;
    public $changelog;
    public $changelogCount;
    public $important = false;

    public function checkChangelog()
    {
        if ($this->changelogCount != 0) {
            $this->emit('isChangelog');
        }
    }

    public function read($changelog_id)
    {
        ChangelogRead::create([
            'uid' => Auth::id(),
            'changelog_id' => $changelog_id
        ]);
        $this->count++;
        $this->important = false;
        if ($this->count > $this->changelogCount) {
            $this->emit('emptyChangelog');
        }
    }

    public function mount()
    {
        $this->count = 1;
        $this->changelog = ModelsChangelog::where('informing', 1)->whereDoesntHave('read', function ($query) {
            $query->where('uid', Auth::id());
        })->get();
        $this->changelogCount = $this->changelog->count();
    }
    
    public function render()
    {
        return view('components.changelog');
    }
}
