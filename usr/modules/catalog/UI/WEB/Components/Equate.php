<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Catalog\UI\WEB\Components;

use Boot\System\Livewire\Component;
use Catalog\Models\Catalog;
use DB;

class Equate extends Component
{
    public $first_field;
    public $second_field;
    public $check = false;
    public $gratz = false;
    public $validate_message = "";
    public $array_first_field;
    public $array_second_field;
    public $max_count;
    public $i = 0;

    public function mount()
    {

    }

    public function render()
    {
        return view('catalog::manager.pump.components.equate');
    }

    public function search()
    {
        $this->check = false;
        $this->gratz = false;
        $this->array_first_field = null;
        $this->array_second_field = null;

        $array_first = explode("\n",$this->first_field);
        $array_second = explode("\n",$this->second_field);
        $count_1 = count($array_first);
        $count_2 = count($array_second);
        $this->max_count = max($count_1,$count_2);
        $this->validate_message = "";

        foreach($array_first as $key => $item)
        {   
            $array_first[$key] = preg_replace("/[^,.0-9]/", '', $item);
            if(str_contains($item, " ") == true || $item == "" || $count_1 != $count_2 || $item != $array_first[$key])  
            {
                $this->validate_message = "Проверь данные!!!";
                $this->gratz = true;
                break;
            }
        }

        if($this->validate_message=="")
        {
            foreach($array_second as $key => $item)
            {   
                $array_second[$key] = preg_replace("/[^,.0-9]/", '', $item);
                if(str_contains($item, " ") == true || $item == "" || $count_1 != $count_2 || $item != $array_second[$key]) 
                {
                    $this->validate_message = "Проверь данные!!!";
                    $this->gratz = true;
                    break;
                }
            }
        }


        if($array_first[0]!='' && $array_second[0]!='' && $this->validate_message=="")
        {
            $this->check = true;   
        

            foreach($array_first as $id)
            {
                $search_first[] = Catalog::find($id);
            }
            
            foreach($array_second as $id)
            {
                $search_second[] = Catalog::find($id);
            }



            foreach($search_first as $item)
            {
                $this->array_first_field[] = array(
                    "id" => $item['id'],
                    "sku" => $item['sku'],
                    "title" => $item['title'],
                );
            }

            foreach($search_second as $key => $item)
            {   
                $data = DB::table("pump")->where('cid',$item['id'])->get()->toArray();
                if(empty($data))
                {
                    $pump = -1;
                    $insert = 1;
                }   
                else
                {
                    $insert = 0;
                    foreach($data as $item_data)
                    {
                        $id_array[] = $item_data->id;
                    }
                    $pump = $id_array;
                }
                    
                $this->array_second_field[] = array(
                    "id" => $item['id'],
                    "sku" => $item['sku'],
                    "title" => $item['title'],
                    'insert' => $insert,
                    'stat_insert' => $insert,
                    'pump_id' => $pump,
                    'key' => $key
                );
            
            }

        }

    }

    public function submit()
    {   
        foreach($this->array_first_field as $key => $item)
        {
            $data = DB::table("pump")->where('cid',$item['id'])->get();
            foreach($data as $ns)
            {   
                //insert == 1 - добавляем, 0 - нет
                //stat_insert == 0 update
                if($this->array_second_field[$key]['insert'] == 1 && $this->array_second_field[$key]['stat_insert'] == 1)
                {   
                    $ns->cid=$this->array_second_field[$key]['id'];
                    unset($ns->id);
                    $ns =  (array) $ns;
                    DB::table("pump")->insert($ns);
                }
                else if($this->array_second_field[$key]['insert'] == 1 && $this->array_second_field[$key]['stat_insert'] == 0)
                {
                    //update
                    $ns->cid=$this->array_second_field[$key]['id'];
                    unset($ns->id);
                    $ns =  (array) $ns;
                    DB::table("pump")->whereIn('id',$this->array_second_field[$key]['pump_id'])->update($ns);
                }
                
            }

        }
        $this->gratz = true;
    }

    public function skip_add($key)
    {   
        if($this->array_second_field[$key]['insert'] === 0)
        {
            $this->array_second_field[$key]['insert'] = 1;
        }
        else {
            $this->array_second_field[$key]['insert'] = 0;
        }
            

    }

    public function close()
    {
        $this->gratz = false;
    }
}
