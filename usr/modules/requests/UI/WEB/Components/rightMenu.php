<?php

namespace Requests\UI\WEB\Components;

use Boot\Models\Users;
use Boot\System\Livewire\Component;
use Boot\Support\Facades\Auth;
use Boot\System\Database\DB;
use FPDF;
use Order\Models\Order;
use Order\Models\OrderHeat;
use Price\Models\CatalogPriceDetail;
use Price\Models\CatalogPriceType;
use Price\UI\CLI\CatalogPriceDaemon;
use Request\Model\Request;
use Requests\Models\Requests;
use Request\Support\RequestSupport;
use Requests\Models\Request as ModelRequest;

class rightMenu extends Component
{
    public
        $unit,
        $count,
        $spec,
        $array_title,
        $priceType,
        $array_assigment,
        $array_units,
        $array_assigment2 = null,
        $editForm = null;

    protected $listeners = ['takeReq', 'editReq', 'endReq'];

    // public $sku,$nds,$price_type,$title,$price,
    // $assigment,$assigment2,$diameter,
    // $burden,$units,$square,$plates,$pressure,$temperature,$weight = null ;

    protected $fields = [
        'sku' => "null",
        'nds' => "null",
        'price' => "null",
        'assigment' => "null",
        'diameter' => "null",
        'burden' => "null",
        'square' => "null",
        'units' => "null",
        'plates' => "null",
        'pressure' => "null",
        'temperature' => "null",
        'weight' => "null",
        'title' => "null",
    ];

    public function mount($unit, $count)
    {
        $this->unit = $unit;
        $this->count = $count;
        $this->spec = json_decode($unit->specification, true);

        foreach ($this->spec as $key => $model) {
            $this->fields[$key] = $model;
        }

        if ($unit->status != 3) {
            $this->editForm = true;
        }
        $this->priceType = CatalogPriceType::where("type", "PRICE")->get();
    }

    public function render()
    {
        if ($this->unit->type == 3) {
            $nn = ["02", "04", "6M", "07", "08", "09", "12M", "14", "18", "19", "20", "21", "21E", "22", "25E", "31", "35E", "36", "37", "41", "42", "43", "44", "45E", "47", "55E", "62", "64", "65", "67", "81", "86", "100", "110", "113", "121", "130", "152", "155", "188"];
            $clh = ["CLH 32S", "CLH 32M", "CLH 50S", "CLH 50M", "CLH 50L", "CLH 65M", "CLH 65S", "CLH 100S", "CLH 100M"];

            $all = array_merge($nn, $clh);

            $this->array_title = $all;
            $this->array_assigment = ["Отопления", "Вентиляции", "ГВС", "Технология"];
            $this->array_units = ["ккал/ч", "Гкал/ч", "кВт", "МВт"];
            $this->array_assigment2 = ["Параллельная", "Моноблок", "1 ступень", "2 ступень"];

        }

        return view('requests::manager.components.rightMenu');
    }

    # Взятие заявки - mode = 0(уведомление) / 1(доступ редактирование)
    public function takeReq($mode = 0)
    {
        if ($mode == 0) {
            $this->dispatchBrowserEvent('take');
        } else {
            $uid = Auth::id();

            //$query = Request::find($this->unit->id);
            $query = Requests::find($this->unit->id);
            if ($query->status == 3) {
                $this->dispatchBrowserEvent('swal', [
                    'title' => "Не получится",
                    'html' => "Заявка уже выполнена",
                    'icon' => 'error',
                ]);
            } else {
                $query->update(['status' => 2, 'uid_exec' => $uid]);
                $this->unit->uid_exec = $query->uid_exec;
                $this->unit->status = $query->status;

                $this->dispatchBrowserEvent('swal', [
                    'title' => "Отлично",
                    'html' => "Заявка взята",
                    'icon' => 'success',
                ]);
                $this->editForm = true;
                $this->emit('statusUpdate', $this->unit->status);
            }
        }

    }

    # Завершение заявки
    public function endReq()
    {


        $uid = Auth::id();

        $id = $this->unit->id;

        $p = new \Price;

        if ($this->unit->type == 3) {
            foreach ($this->spec as $key => $model) {
                $this->fields[$key] = $model;
            }

            if (in_array('null', $this->fields)) {
                $check = false;
            } else {
                $check = true;
                $clh = $this->getTitle("clh");

                $catalog_groups_id = false;

                switch ($this->spec['title']) {
                    default:
                        $type_name = "НН";
                        $catalog_groups_id = 54;
                        break;
                    case in_array($this->spec['title'], $clh):
                        $type_name = "CLH";
                        break;
                }

                $specMark = "{$type_name}№" . $this->spec['title'];

                $title = 'Аппарат теплообменный разборный ' . $specMark;

                $altname = translit($title) . '_' . time();

                $specTitle = 'Аппарат теплообменный разборный';
                $specTitle .= ' ' . $this->spec['assigment'];

                if ($this->spec['assigment'] === "ГВС") {
                    $specTitle .= array_key_exists("assigment2", $this->spec) ? ' ' . $this->spec["assigment2"] : '';
                }

                $specTitle .= ' Ду' . $this->spec["diameter"] . ' мм;';
                $specTitle .= ' Q=' . $this->spec["burden"] . ' ' . $this->spec["units"] . ';';
                $specTitle .= ' S=' . $this->spec["square"] . ' м²;';
                $specTitle .= ' n=' . $this->spec["plates"] . ' шт.;';
                $specTitle .= ' Ру ' . $this->spec["pressure"] . ' МПа;';
                $specTitle .= ' Tmax=' . $this->spec["temperature"] . ' °С;';

                $specArray = ["1" => $specTitle, "2" => $specMark, "3" => ""];

                $weightArray = ["1" => $this->spec["weight"], "2" => "", "3" => "", "4" => "", "5" => ""];

                $short = 'Разборные пластинчатые теплообменники применяются для теплообмена между различными жидкими и газообразными средами. От лучших производителей, удобные в эксплуатации, оптимизируются с необходимыми условиями, имеют малый вес и занимают небольшую площадь.';

                $attach_id_array = [];
                $query = $this->unit->attach->where('module', 'request_catalog');

                if ($query->count() > 0) {
                    foreach ($query as $ai) {
                        $attach_id_array[] = $ai->id;
                    }

                }

                if (empty($this->unit->final_cid)) {
                    $catalog = new \Catalog_v2\Model\Catalog;
                } else {
                    $catalog = \Catalog_v2\Model\Catalog::firstOrNew(["id" => $this->unit->final_cid]);
                }

                $catalog->sku = $this->spec["sku"];
                $catalog->unit = 796;
                $catalog->title = $title;
                $catalog->fulltitle = $title;
                $catalog->short = $short;
                $catalog->uid = $uid;
                $catalog->category = 451;
                $catalog->altname = $altname;
                $catalog->man = 12;
                $catalog->fixed = 1;
                $catalog->types = 3;
                $catalog->active = 0;
                $catalog->active = 0;
                $catalog->spec = $specArray;
                $catalog->weight = $weightArray;
                $catalog->hidden = 0;
                $catalog->preview = '/files/cats/Ridan.jpg';
                $catalog->attach_id = $attach_id_array;

                #Назначение группы товаров
                if ($catalog_groups_id) {
                    $catalog->catalog_groups_id = $catalog_groups_id;
                }

                $catalog->save();

                $cid = $catalog->id;

                $price_type = array_key_exists("price_type", $this->spec) ? $this->spec["price_type"] : 3;

                $CatalogPrice = \Price\Models\CatalogPrice::create(["price_type" => $price_type, "module" => "request", "pid" => $id, "active" => 1]);
                $iid = $CatalogPrice->id;

                $nds_proc = \Price::getNds("3");
                $spec_price = (float)$this->spec["price"];

                if ((int)$this->spec["nds"] === 1) {
                    //цена с ндс
                    $price = $spec_price;
                } else {
                    //цена без ндс (начислить)
                    $price = $spec_price / 100 * ($nds_proc + 100);
                }

                $price = round($price, 2);

                CatalogPriceDetail::create(["cid" => $cid, "price_id" => $iid, "sum" => $price]);
                CatalogPriceDaemon::key_create();

                $this->unit->update(['final_cid' => $cid]);

                #Создание настройки цены
                if ($catalog_groups_id) {
                    RequestSupport::catalogPriceRival($cid);
                }

                $orderHeat = OrderHeat::with('order')->where('request_id', $id)->first();

                if (!empty($orderHeat)) {
                    $attach_id_array = [];

                    $queryA = $this->unit->attach->where('module', 'request_catalog');

                    if ($queryA->count() > 0) {
                        foreach ($queryA as $ai) {
                            $pdf = config('app.url') . '/' . $ai->url;
                        }
                    }

                    $priceType[3] = 'руб.';
                    $priceType[2] = 'EUR';

                    $request_data = json_decode($this->unit->request_data, true);
                    $attr = array();
                    $attr['email'] = $orderHeat->order->email;
                    $attr['name'] = $orderHeat->order->fio_source['result'];
                    $attr['title'] = 'Заявка на расчет №' . $id . ' завершена';
                    $attr['pdf'] = $pdf;
                    $attr['text'] = 'Здравствуйте, <b>' . $attr['name'] . '!</b> <br> Ваша заявка по расчету теплообменного оборудования <b>№' . $id . '</b> завершена. Результаты выполнения     прикреплен во вложении. Стоимость подобранного оборудование на ' . date('d.m.Y') . ' составляет ' . $price . ' ' . $priceType[$price_type] . ' с НДС. Заказать данное оборудование вы можете  связавшись с отделом продаж по  телефону +7(3412)908-123';
                    $attr['url'] = $pdf;
                    $attr['url_text'] = 'Скачать';
                    Order::find($orderHeat->order_id)->update(['status' => '3']);
                    sendEmailByData($attr);

                }


                if ($this->unit->uid_dir != '0') {
                    $arrayClient = [$this->unit->uid_dir, $this->unit->uid_exec];
                    if (($key = array_search($uid, $arrayClient)) !== FALSE) {
                        unset($arrayClient[$key]);
                    }
                    $attr = [];
                    $attr['uid'] = $arrayClient;
                    $attr['title'] = 'Заявка на расчет №' . $id . ' завершена';
                    $attr['text'] = 'Здравствуйте, ' . fullUserName(Auth::id()) . '! Ваша заявка по расчету теплообменного оборудования №' . $id . ' завершена. Результаты выполнения заявки вы можете посмотреть в личном кабинете.';
                    $attr['icon'] = config('app.url') . '/files/push/tasks_ok.png';
                    $attr['url'] = config('app.url') . '/profile/request/view/' . $id;
                    $attr['pNotif'] = true;
                    $attr['pPush'] = true;
                    // sendAllNotif($attr);
                    $attr['uid'] = $this->unit->uid_dir;
                    $attr['text'] = 'Здравствуйте, <b>' . fullUserName(Auth::id()) . '!</b> <br> Ваша заявка по расчету теплообменного оборудования <b>№' . $id . '</b> завершена. Результаты выполнения заявки вы можете посмотреть в личном кабинете.';
                    // sendEmail($attr);
                }


            }

        }

        if ($this->unit->type == 3 && $check == false) {
            $this->dispatchBrowserEvent('swal', [
                'title' => "Внимание",
                'html' => "Не все поля заполнены",
                'icon' => 'info',
            ]);
        } else {
            $this->unit->update(['status' => 3]);

            $this->dispatchBrowserEvent('swal', [
                'title' => "Молодец",
                'html' => "Заявка завершена",
                'icon' => 'success',
            ]);

            $this->editForm = false;

            $this->emit('statusUpdate', 3);
            $this->emit('filesUpdate', 3);
            $this->emit('updateStatusRight', 3);
        }


    }

    # Редактирование расчета теплообмеников - mode = 0(уведомление) / 1(доступ редактирование)
    public function editReq($mode = 0)
    {
        if ($mode == 0) {
            $this->dispatchBrowserEvent('edit');
        } else {
            $this->unit->update(['status' => 2]);
            // $this->dispatchBrowserEvent('updateStatusBar');

            $this->editForm = true;

            $this->emit('statusUpdate', 2);
            $this->emit('filesUpdate', 2);
            $this->emit('updateStatusRight', 2);
        }

    }

    # Распечатка таблиц у насосной станции
    public function print()
    {
        $this->dispatchBrowserEvent('printTable');
    }

    # Форма расчета теплообмеников
    public function editField($field, $value)
    {

        if ($field) {

            if (empty($value)) {
                if (array_key_exists($field, $this->spec)) {
                    unset($this->spec[$field]);
                }
            } else {
                $this->spec[$field] = $value;
            }

            if (!array_key_exists("nds", $this->spec)) {
                $this->spec["nds"] = 0;
            }
            if (!array_key_exists("price_type", $this->spec)) {
                $this->spec["price_type"] = 3;
            }

            $spec = json_encode($this->spec, JSON_UNESCAPED_UNICODE);

            $this->unit->update(['specification' => $spec]);

            $this->dispatchBrowserEvent('editField');
        }

    }

    # а это ХЗ
    function getTitle($type = "all")
    {
        $nn = ["02", "04", "6M", "07", "08", "09", "12M", "14", "18", "19", "20", "21", "21E", "22", "25E", "31", "35E", "36", "37", "41", "42", "43", "44", "45E", "47", "55E", "62", "64", "65", "67", "81", "86", "100", "110", "113", "121", "130", "152", "155", "188"];
        $clh = ["CLH 32S", "CLH 32M", "CLH 50S", "CLH 50M", "CLH 50L", "CLH 65M", "CLH 65S", "CLH 100S", "CLH 100M"];

        $all = array_merge($nn, $clh);

        if ($type == "all") {
            return $all;
        } else {
            return isset($$type) ? $$type : $all;
        }
    }

}
