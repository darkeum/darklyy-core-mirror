<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Decrees\UI\WEB\Components;

use Boot\Models\Attach;
use Boot\System\Livewire\Component;

class DeleteFiles extends Component
{
    public $unit,$fid,$file,$fileName,$mode;

    public function mount($unit,$fid,$mode = 0)
    {
       if($fid == null)
       {
            $this->fileName = "Файл отсутствует";
       }
       else
       {
            $this->file = Attach::find($fid);
            $this->fileName = $this->file->name;
            $this->mode = $mode;
            $this->unit = $unit;
       }
    }

    public function render()
    {
        return view('decrees::manager.components.DeleteFiles');
    }

}
