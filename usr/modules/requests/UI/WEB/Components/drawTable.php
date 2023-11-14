<?php

namespace Requests\UI\WEB\Components;

use Boot\System\Livewire\Component;
use Requests\Models\PumpStationDrawning;
use Illuminate\Support\Facades\Storage;
use Boot\Models\Attach;

class drawTable extends Component
{
    public $unit = null;
    public $files = null;
    public $currentData;

    protected $listeners = ['deleteDraw', 'uploadDraw', 'refreshFiles'];

    public function mount($unit)
    {
        $this->unit = $unit;

        $this->currentData = collect(
            [
                'cid'=> $unit->result[0]['info']['cid'],
                'dn_kol'=> $unit->result[0]['info']['dn_kol'],
                'count'=> $unit->result[0]['info']['pump_count']
            ]
        );
        $this->refreshFiles();
    }

    public function render()
    {
        return view('requests::manager.tables.drawTable');
    }

    private function refreshFiles()
    {
        $this->files = PumpStationDrawning::
        where('cid','=',$this->currentData->get('cid'))
        ->where('dn_kol','=',$this->currentData->get('dn_kol'))
        ->where('count','=',$this->currentData->get('count'))
        ->get()
        ;
        foreach($this->files as $key => $file)
        {
            $this->files[$key]->setAttribute('size', storage_size($file->attach->url));
        }
    }

    //добавление файла
    public function uploadDraw($fileData, $fileName)
    {
        $path_folder = "files/request/draw/";

        Storage::disk('local')->putFileAs(
            $path_folder,
            $fileData,
            $fileName,
        );

        $info["name"] = $fileName;
        $info["url"] = $path_folder . $fileName;
        $info["module"] = "request";

        $attach = Attach::create($info);
        $newDraw = PumpStationDrawning::create([
            'file_id' => $attach->id,
            'cid' => $this->currentData->get('cid'),
            'dn_kol' => $this->currentData->get('dn_kol'),
            'count' => $this->currentData->get('count'),
        ]);

        $this->dispatchBrowserEvent('drawSwal', [
            'message' => "Файл добавлен",
        ]);

        $this->refreshFiles();
    }

    // удаление файла
    // fid - id file
    // mode = 0 - предупрежедние. mode = 1 - удаление
    public function deleteDraw($fid, $mode = 0)
    {   
        if($mode == 0)
        {          
            $this->dispatchBrowserEvent('deleteDraw', [
                'fid' => $fid
            ]);
        }
        else 
        {
            Attach::find($fid)->delete();
            $this->files->where('file_id','=',$fid)->first()->delete();

            $this->dispatchBrowserEvent('drawSwal', [
                'message' => "Файл удалён",
            ]);

            $this->refreshFiles();
        }
    
    }
    
}
