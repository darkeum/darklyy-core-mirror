<?php

namespace Requests\UI\WEB\Components;

use Boot\Models\Attach;
use Boot\System\Livewire\Component;
use Illuminate\Support\Facades\Storage;

class filesTable extends Component
{
    public $files, $unit = null;
        
    protected $listeners = ['deleteFile', 'fileUpload', 'filesUpdate' => 'updateStatus'];
    
    public function mount($unit)
    {   
        $this->files = $unit->attach;
        $this->unit = $unit;
    }

    public function render()
    {
        
        foreach ($this->files as $key => $file) {
            $this->files[$key]->setAttribute('size', storage_size($file['url']));
        }

        return view('requests::manager.components.filesTable');
    }

    public function updateStatus($status)
    {
       $this->unit->status = $status;
    }

    //добавление файла
    public function fileUpload($fileData, $fileName)
    {
        $path_folder = "files/request/".$this->unit->id."/";

        Storage::disk('local')->putFileAs(
            $path_folder,
            $fileData,
            $fileName,
        );

        $info["name"] = $fileName;
        $info["url"] = $path_folder . $fileName;
        $info["pid"] = $this->unit->id;
        $info["size"] = storage_size($info["url"]);
        $info["module"] = "request";

        $attach = Attach::create($info);

        $this->files->push($attach);

    }

    // удаление файла
    // fid - id file
    // mode = 0 - предупрежедние. mode = 1 - удаление
    public function deleteFile($fid, $mode = 0)
    {   
        if($mode == 0)
        {          
            $this->dispatchBrowserEvent('deleteFile', [
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
    
    
}
