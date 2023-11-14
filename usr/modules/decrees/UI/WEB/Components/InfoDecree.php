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


use Boot\System\Livewire\Component;

class InfoDecree extends Component
{
    public $unit, $data;

    public $city, $org, $short_report, $post, $target, $date_transit = null;
    public $dis = true;
    public $inputs = [];
    public $i = 0;

    protected $listeners = ['changeType'];

    public function mount($unit)
    {

        $this->unit = $unit;
        $this->data = $unit->data ?? null;

        if($unit != null && $unit->type == 3)
        {
            $this->dis = false;
        }

        if ($this->data != null) {
            $this->post = $unit->data['post'] ?? null;
            $this->target = $unit->data['target'] ?? null;
            $this->date_transit = $unit->data['date_transit'] ?? null;


            array_push($this->inputs, $this->i);
            foreach ($this->data['destination'] as $key => $info) {
                $this->data['city'][$key] = $info['city'] ?? null;
                $this->data['org'][$key] = $info['org'] ?? null;
                $this->data['short_report'][$key] = $info['short_report'] ?? null;

                $this->city[$key] = $info['city'] ?? null;
                if(isset($info['org']))
                {
                    $this->org[$key] = preg_replace("/&#?[a-z0-9]+;/i",'"',$info['org']) ;
                }
                else
                {
                    $this->org[$key] = null;
                }
                
                $this->short_report[$key] = $info['short_report'] ?? null;
                if ($key != 0) {
                    array_push($this->inputs, $this->i);
                }
                $this->i = $this->i + 1;
            }
        } else {
            array_push($this->inputs, $this->i);
        }
    }

    public function render()
    {
        return view('decrees::manager.components.InfoDecree');
    }

    public function add($i)
    {
        $i = $i + 1;
        $this->i = $i;
        array_push($this->inputs, $i);
    }

    public function remove($i)
    {
        unset($this->inputs[$i]);
    }

    public function updated()
    {
        foreach ($this->inputs as $key => $value) {

            if (isset($this->city[$value])  || isset($this->org[$value]) || isset($this->short_report[$value])) {
                $this->data['city'][$value] = $this->city[$value] ?? null;
                $this->data['org'][$value] = $this->org[$value] ?? null;
                $this->data['short_report'][$value] = $this->short_report[$value] ?? null;
            }
        }
    }

    public function changeType($dis)
    {
        $this->dis = $dis;
    }
}
