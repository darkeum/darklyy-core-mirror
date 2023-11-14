<?php

namespace Reestr\UI\WEB\Components;

use Boot\System\Livewire\Component;

use Boot\Models\Attach;
use Boot\Support\Carbon;
use Objects\Models\Objects;
use Organizations\Model\Organizations;
use Orgs\Models\Org;
use Reestr\Models\ReestrDoc;
use Reestr\Models\ReestrType;
use Reestr\Support\ReestrSupport;
use Relations;

use Illuminate\Support\Collection;


use Boot\System\Livewire\WithFileUploads;
use Reestr\Models\Reestr;
use Sales\Support\SalesReestrSupport;

class ModalInfo extends Component
{
    use WithFileUploads;

    protected $listeners = ['selectBlockModal', 'deleteFile'];

    public $typesForAdd;         // тип договор на добавление
    public $selectedForm = null; // информация о выбранном документе
    public $docform;

    public $newDoc, $newScan; // новые документы

    public $relations = false;
    public $resultRel;
    public $formRel;

    public $unitId;

    public function mount()
    {
        $this->typesForAdd = config('reestr-config.types')['text'];
    }

    public function render()
    {
        return view('reestr::manager.components.modalinfo');
    }

    public function selectBlockModal($fid, $unitId)
    {
        $this->emit('reloadFields');
        $this->unitId = $unitId;
        $this->selectedForm = ReestrDoc::where('id', '=', $fid)->with('docFile')->with('scanFile')->first();
        $this->docform['date'] = Carbon::parse($this->selectedForm->date)->format('d.m.Y');
        $this->docform['comment'] = $this->selectedForm->comment;
        $this->docform['status'] = $this->selectedForm->status;
        $this->docform['uid_took'] = $this->selectedForm->uid_took;

        if ($this->selectedForm->type == 2) {
            $rel = new Relations;
            $resultRel = $rel->find(["module_in" => "reestr_spec", "pid_in" => $fid, "module_out" => "sales"]);
            $this->relations = false;
            if (count($resultRel) > 0) {
                $this->relations = true;
                $this->formRel['sales_id'] = $resultRel[0]['pid_out'];
            }
        }
        $this->emit('reloadFieldsModal');
    }

    public function UpdatedDocform()
    {
        if ($this->docform['date']) {
            Carbon::parse($this->docform['date'])->format('Y.m.d');
        }
        $array['comment'] = $this->docform['comment'];
        $array['status'] = $this->docform['status'];
        $array['uid_took'] = $this->docform['uid_took'] ?? null;

        $this->selectedForm->update($array);


        $this->emit('checkSign');
        $this->emit('checkScan');
        $this->emit('blockDocs');

        $this->emit('docFormUpdated');
        $this->emit('reloadFields');
        $this->emit('reloadFieldsModal');
    }

    public function signDoc()
    {
        $this->selectedForm->update([
            'sign' => $this->selectedForm->sign == 1 ? 0 : 1
        ]);

        $this->emit('checkSign');
        $this->emit('docFormUpdated');
        $this->emit('reloadFields');
        $this->emit('reloadFieldsModal');
    }

    public function UpdatedNewDoc()
    {

        $path_folder = "reestr/" . $this->unitId . "/" . $this->selectedForm->id . "/";
        $fileName = $this->newDoc->getClientOriginalName();

        $this->newDoc->storeAs(
            $path_folder,
            $fileName,
            'files',
        );

        $fileId = Attach::create([
            "name" => $fileName,
            "url" => 'files/' . $path_folder . $fileName,
            "pid" => $this->unitId,
            "module" => 'reestr',
        ]);

        $this->selectedForm->update([
            'doc' => $fileId->id,
        ]);

        $this->newDoc->delete();
        $this->newDoc = null;

        $this->selectBlockModal($this->selectedForm->id, $this->unitId);

        $this->emit('checkScan');
        $this->emit('blockDocs');

        $this->emit('docFormUpdated');
        $this->emit('reloadFields');
        $this->emit('reloadFieldsModal');
    }

    public function UpdatedNewScan()
    {

        $path_folder = "reestr/" . $this->unitId . "/" . $this->selectedForm->id . "/";
        $fileName = $this->newScan->getClientOriginalName();

        $this->newScan->storeAs(
            $path_folder,
            $fileName,
            'files',
        );

        $fileId = Attach::create([
            "name" => $fileName,
            "url" => 'files/' . $path_folder . $fileName,
            "pid" => $this->unitId,
            "module" => 'reestr',
        ]);

        $this->selectedForm->update([
            'scan' => $fileId->id,
        ]);

        $this->newScan->delete();
        $this->newScan = null;

        $this->selectBlockModal($this->selectedForm->id, $this->unitId);

        $this->emit('checkScan');
        $this->emit('blockDocs');

        $this->emit('docFormUpdated');
        $this->emit('reloadFields');
        $this->emit('reloadFieldsModal');
    }

    public function deleteFile($mode)
    {
        $fileID = $this->selectedForm->$mode;

        Attach::find($fileID)->delete();
        $this->selectedForm->update([
            $mode => null,
        ]);

        $this->selectBlockModal($this->selectedForm->id, $this->unitId);

        $this->emit('checkScan');
        $this->emit('blockDocs');

        $this->emit('docFormUpdated');
        $this->emit('reloadFields');
        $this->emit('reloadFieldsModal');
    }

    public function createRel()
    {
        if ($this->formRel['delivery'] == 1) {
            $this->formRel['reestr_delivery_address'] = null;
        }
        $this->formRel['reestr_id'] = $this->unitId;
        $this->formRel['reestr_doc_id'] = $this->selectedForm->id;
        $fid = SalesReestrSupport::get_spec($this->formRel); // return file id
        $this->selectedForm->update([
            'doc' => $fid,
        ]);
        $this->formRel = null;

        $this->emit('blockDocs');

        $this->emit('docFormUpdated');
        $this->emit('reloadFields');
        $this->emit('reloadFieldsModal');
    }
}
