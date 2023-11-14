<?php

namespace Reestr\UI\WEB\Components;

use Boot\Models\Attach;
use Boot\Support\Carbon;
use Boot\System\Livewire\Component;
use Objects\Models\Objects;
use Organizations\Model\Organizations;
use Orgs\Models\Org;
use Reestr\Models\ReestrDoc;
use Reestr\Models\ReestrType;
use Reestr\Support\ReestrSupport;
use Relations;

use Illuminate\Support\Facades\Storage;


use Boot\System\Livewire\WithFileUploads;
use Reestr\Models\Reestr;
use Sales\Support\SalesReestrSupport;

use function Amp\Dns\query;

class info extends Component
{
    use WithFileUploads;

    protected $listeners = ['formUpdated', 'deleteBlock', 'resetCount', 'createRel' ,
    'blockDocs','checkSign','checkScan'];

    public $unit, $orgs, $typeList;        // общие переменные
    public $org, $contragent, $obj, $type; // таблица с информацией

    public $form; //быстрое редактирование информации

    public $selectType; // поле для создания документа

    public $typesForAdd,$tableTypes;         // тип договор на добавление
    public $docs;                // созданные договора
    public $docsTree;            // дерево с договорами
    public $docsInCategoty = [];

    public $relations = false;
    public $resultRel;
    public $formRel;


    public function mount($unit)
    {
        $this->typesForAdd = config('reestr-config.types')['text'];
        $this->tableTypes = config('reestr-config.types')['textSolo'];
        $this->unit = $unit;
        $this->contragent = Organizations::find($this->unit->oid);
        $this->org = Org::find($this->unit->org);
        $this->obj = $this->unit->object != null ? Objects::find($this->unit->object) : false;
        $this->type = ReestrType::find($this->unit->type);
        $this->orgs = Org::with('organization')->get();
        $this->blockDocs();
        $this->typeList = ReestrType::where('org', '=', $this->unit->org)->get();

    }

    public function render()
    {

        return view('reestr::manager.components.info');
    }


    public function formUpdated()
    {
        $this->form = array_filter($this->form);
        if ($this->form['date_view'] ?? false) {
            $this->form['date_view'] = Carbon::parse($this->form['date_view'])->format('Y.m.d');
        }
        $this->unit->update($this->form);
        $this->unit = Reestr::find($this->unit->id);
        $this->contragent = Organizations::find($this->unit->oid);
        $this->org = Org::find($this->unit->org);
        $this->obj = $this->unit->object != null ? Objects::find($this->unit->object) : false;
        $this->type = ReestrType::find($this->unit->type);
        $this->blockDocs();
        $this->emit('reloadFields');
    }


    public function UpdatedSelectType()
    {

        $array = [
            'type' => $this->selectType,
            'reestr_id' => $this->unit->id,
        ];
        ReestrSupport::add_doc($array);

        if ($this->selectType == 2) {
            $this->unit->update(['spec_numb_counter' => $this->unit->spec_numb_counter != null ? $this->unit->spec_numb_counter + 1 : 1]);
        }

        $this->checkSign();
        $this->checkScan();
        $this->blockDocs();
        $this->emit('reloadFields');
    }

    public function deleteBlock($blockId)
    {
        $block = $this->docs[$blockId];
        $blockId = $block->id;
        if ($block->doc != null) {
            Attach::find($block->doc)->delete();
        }
        if ($block->scan != null) {
            Attach::find($block->scan)->delete();
        }
        $block->delete();

        $rel = new Relations;
        $rel->delete(["module_in" => "reestr_spec", "pid_in" => $blockId, "module_out" => "sales"]);

        $this->blockDocs();

        $this->checkSign();
        $this->checkScan();
        $this->emit('docFormUpdated');
        $this->emit('reloadFields');

    }


    public function resetCount()
    {

        $unit = ReestrDoc::where('reestr_id', '=', $this->unit->id)
            ->where('type', '=', 2)
            ->first();
        $counter = 0;
        if ($unit != null) {
            $counter = $unit->spec_numb;
        }
        $this->unit->update(['spec_numb_counter' => $counter]);

        $this->emit('docFormUpdated');
        $this->emit('reloadFields');
        $this->blockDocs();
    }

    public function checkSign()
    {

        $count = ReestrDoc::where('reestr_id', '=', $this->unit->id)->count();

        $count_sign = ReestrDoc::where('reestr_id', '=', $this->unit->id)->where('sign', '=', 1)->count();

        if ($count == $count_sign) {
            $value = 2;
        }
        if ($count != $count_sign) {
            $value = 1;
        }
        if ($count_sign == 0) {
            $value = 0;
        }

        $this->unit->update(['sign' => $value]);
        $this->blockDocs();
    }

    public function checkScan()
    {

        $count = ReestrDoc::where('reestr_id', '=', $this->unit->id)->count();

        $count_scan = ReestrDoc::where('reestr_id', '=', $this->unit->id)->where('scan', '!=', NULL)->count();

        if ($count == $count_scan) {
            $value = 2;
        }
        if ($count != $count_scan) {
            $value = 1;
        }
        if ($count_scan == 0) {
            $value = 0;
        }

        $this->unit->update(['scan' => $value]);
        $this->blockDocs();
    }

    public function blockDocs()
    {
        $conf = config('reestr-config.types');
        $query = $this->unit->docs()->with('docFile')->with('scanFile')->get();

        if ($query->isNotEmpty() ?? false) {
            $this->docsInCategoty = null;
            foreach ($query as $key => $item) {

                $this->docsInCategoty[$conf['text'][$item->type]][] = $item;
            }
        //     // Пушер данных
        //     $pusher = function ($text, $children, $type, $state = false) {
        //         $child = collect();
        //         // $child->put('text', $text);

        //         if ($text) {
        //             $child->put('text', $text);
        //             //$child->put('id', $id);

        //             if ($type) {
        //                 $child->put('type', $type);
        //             }

        //             if ($state) {
        //                 $child->put('state', collect(['opened' => true]));
        //             }

        //             if (is_array($children) or $children instanceof Collection) {
        //                 $child->put('children', collect($children));
        //             }

        //         }

        //         return $child;
        //     };

        //     $tree = collect();
        //     $tree
        //         ->put('core', collect())
        //         ->put('types', collect($conf['icons']))
        //         ->put('plugins', collect(['types']))
        //         ->get('core')
        //         ->put('data', collect());

        //     $tree['core']['data'] = $pusher('Документы', [], 'default', true);


        //     $counter = 0;
        //     foreach ($docs as $key => $item) {

        //         $tree['core']['data']['children']
        //             ->push($pusher($key, [], 'default', true));

        //         foreach ($item as $subcounter => $subitem) {

        //             $docFile = $pusher($subitem->docFile->name ?? '<a class="text-warning">Нет</a>', false, 'docx', true);
        //             $scanFile = $pusher($subitem->scanFile->name ?? '<a class="text-warning">Нет</a>', false, 'pdf', true);

        //             $nameFolder = $conf['textSolo'][$subitem->type];
        //             if ($subitem->type == 2) {
        //                 $nameFolder .= " #" . $subitem->spec_numb;
        //             }

        //             $tree['core']['data']['children']
        //                 ->get($counter)->get('children')
        //                 ->push($pusher($nameFolder, [$docFile, $scanFile], 'default', true));
        //         }
        //         $counter += 1;
        //     }

        //     $this->docsTree = $tree->toJson(JSON_UNESCAPED_UNICODE);
        }
        $this->docs =  $query->isNotEmpty() ? $query : null;
    }

}
