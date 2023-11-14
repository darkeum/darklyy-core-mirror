<?php

namespace Requests\UI\WEB\Components;

use Boot\Models\Attach;
use Boot\System\Livewire\Component;
use Illuminate\Support\Facades\Storage;

class requestsFiles extends Component
{
    public $files, $unit = null;
        
    protected $listeners = ['deleteFileRight', 'fileUploadRight',"finalFileRight",'updateStatusRight'];
    
    public function mount($unit)
    {   
        $this->files = $unit->attachRequestCatalog;
        $this->unit = $unit;
    }

    public function render()
    {
        
        foreach ($this->files as $key => $file) {
            $this->files[$key]->setAttribute('size', storage_size($file['url']));
        }

        return view('requests::manager.components.requestsFiles');
    }

    public function updateStatusRight($status)
    {
       $this->unit->status = $status;
    }

    //добавление файла
    public function fileUploadRight($fileData, $fileName)
    {
        $path_folder = "files/request/".$this->unit->id."/catalog/";

        Storage::disk('local')->putFileAs(
            $path_folder,
            $fileData,
            $fileName,
        );

        $info["name"] = $fileName;
        $info["url"] = $path_folder . $fileName;
        $info["pid"] = $this->unit->id;
        $info["size"] = storage_size($info["url"]);
        $info["module"] = "request_catalog";

        $attach = Attach::create($info);

        $this->files->push($attach);

        $this->emit('treeUpdate', $this->files);
    }


    // удаление файла
    // fid - id file
    // mode = 0 - предупрежедние. mode = 1 - удаление
    public function deleteFileRight($fid, $mode = 0)
    {   
        if($mode == 0)
        {   
            
            $this->dispatchBrowserEvent('deleteFileRight', [
                'fid' => $fid
            ]);
        }
        else 
        {
            
            $file = Attach::find($fid);
            Storage::disk("local")->delete($file['url']);
            $file->delete();
            foreach($this->files as $key => $file)
            {
                if($file['id'] == $fid)
                {
                    unset($this->files[$key]);
                    break;
                }
            }
        }
       
    }


     // Финальный файл
    // fid - id file
    // mode = 0 - предупрежедние. mode = 1 - подтверждение
    public function finalFileRight($fid, $mode = 0)
    {   
        if($mode == 0)
        {   
            
            $this->dispatchBrowserEvent('finalFileRight', [
                'fid' => $fid
            ]);
        }
        else 
        {
           $this->unit->update(['aid' => $fid, 'status' => 3]);
           $this->emit('endReq');
        }
       
    }
    
    
}
