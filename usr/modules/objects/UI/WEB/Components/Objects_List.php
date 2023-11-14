<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Objects\UI\WEB\Components;

use Boot\Support\Carbon;
use Illuminate\Pagination\Paginator;
use Objects\Models\Objects;
use Boot\System\Livewire\Component;
use Boot\System\Livewire\WithPagination;


class Objects_List extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $objects;

    public function mount()
    {
        //Paginator::useBootstrapFive();
        //$this->objects = Objects::where('coords', '<>', '')->paginate(10);
         $this->objects = Objects::where('coords', '<>', '')
             ->orderBy('id', 'desc')
             ->get();
            //->reverse();
    }

    public function render()
    {
        return view('objects::manager.components.objects', [
             'objects' => $this->objects
        ]);
    }

    public function paginationView()
    {
        return 'components.paginator';
    }

}
