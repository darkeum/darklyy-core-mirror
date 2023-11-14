<?php

namespace Economic;

trait ActController{
    protected $records;
    protected $org_data;
    protected $org_director;
    protected $oid_data;
    protected $debit_total;
    protected $credit_total;
    protected $initial_balance;
    protected $initial_balance_contract;

    public $id = false;
    public $numb;
    public $date;
    public $date_start;
    public $date_end;
    public $final_balance = 0;
    public $break_contracts;
    public $display_full_document_titles;
    public $display_invoices;
    public $org_director_full;
    public $oid_director_full;
    public $comment;
    public $fill_type;
    public $agreed;
    public $active;

    private function middleware(){
        if(empty($this->org)) throw new \Exception("Организация не указана");
        if(empty($this->oid)) throw new \Exception("Контрагент не указан");
        if(empty($this->date_start)) throw new \Exception("Дата начала не указана");
        if(empty($this->date_end)) throw new \Exception("Дата конца не указана");
        if(strtotime($this->date_start) > strtotime($this->date_end)) throw new \Exception("Дата окончания периода сверки должна быть больше даты начала");
    }

    public function init_act(){
        if(empty($this->id)) throw new \Exception("Empty `id`");

        $ActReconciliation = \Economic\Model\ActReconciliation::find($this->id);

        $this->date =                           $ActReconciliation->date;
        $this->date_start =                     $ActReconciliation->date_start;
        $this->date_end =                       $ActReconciliation->date_end;
        $this->org =                            $ActReconciliation->org;
        $this->oid =                            $ActReconciliation->oid;
        $this->org_data =                       $ActReconciliation->org_data;
        $this->org_director =                   $ActReconciliation->org_director;
        $this->oid_data =                       $ActReconciliation->oid_data;
        $this->initial_balance =                $ActReconciliation->initial_balance;
        $this->initial_balance_contract =       $ActReconciliation->initial_balance_contract;
        $this->final_balance =                  $ActReconciliation->final_balance;
        $this->numb =                           $ActReconciliation->numb;
        $this->comment =                        $ActReconciliation->comment;
        $this->fill_type =                      $ActReconciliation->fill_type;
        $this->agreed =                         $ActReconciliation->agreed;
        $this->break_contracts =                $ActReconciliation->break_contracts;
        $this->display_full_document_titles =   $ActReconciliation->display_full_document_titles;
        $this->display_invoices =               $ActReconciliation->display_invoices;
        $this->records =                        $ActReconciliation->records;
        $this->uid =                            $ActReconciliation->uid;
        $this->active =                         $ActReconciliation->active;
        $this->dc_calc();
        $this->init_date_view();
        $this->init_org();
        $this->init_oid();
    }

    static public function price_format($price){
        return is_null($price) ? null : number_format($price, 2, ",", " ");
    }

    private function init_data(){
        $this->middleware();

        // $this->get_initial_balance();

        $this->init_date_view();

        $this->get_org();
        $this->get_oid();
    }

    public function init_date_view(){
        $this->date_view = date("d.m.Y H:i:s", strtotime($this->date));
        $this->date_start_view = date("d.m.Y", strtotime($this->date_start));
        $this->date_end_view = date("d.m.Y", strtotime($this->date_end));
    }

    // public function get_initial_balance(){
    //     $balance = \Economic\Model\Balance::get_balance($this->org, $this->oid, $this->date_start);
    //     $this->initial_balance = $balance == null ? 0 : $balance->balance;
    // }

    public function get_initial_balance(){
        $this->initial_balance = 0;
        $this->initial_balance_contract = [];
        if(is_array($this->records)){
            foreach($this->records as $record){
                $this->initial_balance_contract[ $record->contract_id ] = null;
            }
            foreach($this->initial_balance_contract as $contract_id => $balance_null){
                $b = \Economic\Model\Balance::get_balance($this->org, $this->oid, $this->date_start, empty($contract_id) ? null : $contract_id);
                $this->initial_balance += empty($b) ? 0 : $b->balance;
                $this->initial_balance_contract[ $contract_id ] = (object)[
                    "id" => $b->id ?? null,
                    "balance" => $b->balance ?? null,
                ];
            }
        }
        return $this->initial_balance;
    }

    public function get_org(){
        $this->org_ = \Organizations\Model\Org::find($this->org);
        $this->org_data = $this->org_->organization;
        $this->org_director = $this->org_->director_user;
        $this->init_org();
    }

    public function init_org(){
        $this->org_director_full = "{$this->org_director->surname} {$this->org_director->name} {$this->org_director->ochestvo}";
        $this->org_director_short = "{$this->org_director->surname} ".mb_substr($this->org_director->name, 0, 1).". ".mb_substr($this->org_director->ochestvo, 0, 1).".";
    }

    public function get_oid(){
        $this->oid_data = \Organizations\Model\Organizations::find($this->oid);
        $this->init_oid();
    }

    public function init_oid(){
        $director_detail = explode(" ", $this->oid_data->director);
        $this->oid_director_full = "{$director_detail[0]} {$director_detail[1]} {$director_detail[2]}";
        $this->oid_director_short = "{$director_detail[0]} ".mb_substr($director_detail[1], 0, 1).". ".mb_substr($director_detail[2], 0, 1).".";
        $this->oid_post = mb_ucfirst(mb_strtolower($this->oid_data->post));
    }

    public function calc_final_balance($initial, $debit, $credit){
        return round($initial - ($credit - $debit), 2);
    }

    public function get_final_balance(){
        $this->final_balance = $this->calc_final_balance($this->initial_balance, $this->debit_total, $this->credit_total);
    }

    public function get_records(){
        $this->records = [];
        /* data collection */
        $this->receipts();
        $this->banks_recipient();
        $this->banks_payer();
        $this->realizations();
        $this->corrections();
        /* data collection end */
        if($this->break_contracts){
            $this->contracts_records_sort();
        } else {
            $this->records_sort();
        }
        $this->dc_calc();
        return $this->records;
    }

    public function dc_calc(){
        $this->debit_total = 0;
        $this->credit_total = 0;
        foreach($this->records as $item){
            $this->debit_total += $item->debit;
            $this->credit_total += $item->credit;
        }
    }

    private function records_sort(){
        usort($this->records, function($a, $b){
            if ($a->date == $b->date) return 0;
            return (strtotime($a->date) < strtotime($b->date)) ? -1 : 1;
        });
    }

    private function contracts_records_sort(){
        $args = [
            // "contract_id" => [
            //     "sort" => "asc",
            // ],
            "contract_name" => [
                "sort" => "desc",
            ],
            "date" => [
                "sort" => "asc",
                "prepare_value" => function($value){
                    return strtotime($value);
                },
            ]
        ];
        usort($this->records, function($a, $b) use ($args){
            $res = 0;
            foreach( $args as $k => $v ){
                $v1 = $a->$k;
                $v2 = $b->$k;
                if(isset($v["prepare_value"])){
                    $pv = $v["prepare_value"];
                    $v1 = $pv($v1);
                    $v2 = $pv($v2);
                }
                if($v1 == $v2) continue;

                $res = ( $v1 < $v2 ) ? -1 : 1;
                if( $v["sort"] == 'desc' ) $res= -$res;
                break;
            }
            return $res;
        });
    }

    static function format_module_decode($module){
        return str_replace("/", "\\", $module);
    }

    static function format_module_encode($module){
        return str_replace("\\", "/", $module);
    }

    private function receipts(){
        $receipts = \Supply\Model\Receipts::where("oid", $this->oid)
        ->where("org", $this->org)
        ->where("active", 1)
        ->whereDate("date_receipts", ">=", $this->date_start)
        ->whereDate("date_receipts", "<=", $this->date_end)
        ->groupBy("doc_number")
        ->select(\DB::raw("*, SUM(total) as total"))
        ->get();
        foreach($receipts as $receipt){
            $item = static::create_item();
            $item->id = $receipt->id;
            $item->module_out = "supply_order";
            $item->pid_out = $receipt->supply_id;
            $item->class_name = static::format_module_encode(\Supply\Model\Receipts::class);
            $item->module = "receipt";
            $item->uid = $receipt->uid;
            $date = date("d.m.Y", strtotime($receipt->date_receipts));
            $item->date = $date;
            $item->org_data->document = "Приход ({$receipt->doc_number} от {$date})";
            $item->oid_data->document = "Продажа ({$receipt->doc_number} от {$date})";
            $item->org_data->document_full = $item->oid_data->document_full = "Поступление (акт, накладная, УПД) {$receipt->doc_number} от {$date}";
            $item->credit = $receipt->total;
            $item->link = "/administration/module/supply/receipts_detail/{$receipt->id}";
            array_push($this->records, $item->complete());
        }
    }

    private function banks_recipient(){
        $banks = \General\Models\BankDetail::where("recipient", $this->oid)
        ->where("payer", $this->org_data->id)
        ->whereDate("date", ">=", $this->date_start)
        ->whereDate("date", "<=", $this->date_end)
        ->get();
        foreach($banks as $bank){
            $item = static::create_item();
            $item->id = $bank->id;
            $item->module_out = "bank_statement";
            $item->pid_out = $bank->id;
            $item->class_name = static::format_module_encode(\General\Models\BankDetail::class);
            $item->module = "bank";
            $item->uid = 24;
            $date = date("d.m.Y", strtotime($bank->date));
            $item->date = $date;
            $item->org_data->document = $item->oid_data->document = "Оплата ({$bank->numb} от {$date})";
            $item->org_data->document_full = $item->oid_data->document_full = "Списание с расчетного счета от {$date}";
            $item->debit = $bank->sum;
            $item->link = "/administration/bank/edit/{$bank->id}";
            array_push($this->records, $item->complete());
        }
    }

    private function banks_payer(){
        $banks = \Bank\Models\BankDetail::where("payer", $this->oid)
        ->where("recipient", $this->org_data->id)
        ->whereDate("date", ">=", $this->date_start)
        ->whereDate("date", "<=", $this->date_end)
        ->get();
        foreach($banks as $bank){
            $item = static::create_item();
            $item->id = $bank->id;
            $item->module_out = "bank_statement";
            $item->pid_out = $bank->id;
            $item->class_name = static::format_module_encode(\Bank\Models\BankDetail::class);
            $item->module = "bank";
            $item->uid = 24;
            $date = date("d.m.Y", strtotime($bank->date));
            $item->date = $date;
            $item->org_data->document = $item->oid_data->document = "Оплата ({$bank->numb} от {$date})";
            $item->org_data->document_full = $item->oid_data->document_full = "Поступление на расчетного счета от {$date}";
            $item->credit = $bank->sum;
            $item->link = "/administration/bank/edit/{$bank->id}";
            array_push($this->records, $item->complete());
        }
    }

    private function realizations(){
        $realizations = \Realization\Model\Realization::where("oid", $this->oid)
        ->where("org", $this->org)
        ->where("active", 1)
        ->whereDate("date", ">=", $this->date_start)
        ->whereDate("date", "<=", $this->date_end)
        ->get();
        foreach($realizations as $realization){
            $item = static::create_item();
            $date = date("d.m.Y", strtotime($realization->date));
            $item->id = $realization->id;
            $item->module_out = "sales";
            $item->pid_out = $realization->sales_id;
            $item->class_name = static::format_module_encode(\Realization\Model\Realization::class);
            $item->module = "realization";
            $item->uid = $realization->uid;
            $item->date = $date;
            $item->org_data->document = "Продажа ({$realization->numb_counter} от {$date})";
            $item->oid_data->document = "Приход ({$realization->numb_counter} от {$date})";
            $item->org_data->document_full = $item->oid_data->document_full = "Реализация (акт, накладная, УПД) {$realization->numb} от {$date}";
            $item->debit = $realization->total;
            $item->link = "/administration/module/realization/order/{$realization->id}";
            array_push($this->records, $item->complete());
        }
    }

    private function corrections(){
        global $db;
        $corrections = \Realization\Model\Correct::whereHas("realization", function($q){
            $q->where("oid", $this->oid);
        })->where("org", $this->org)
        ->where("active", 1)
        ->whereDate("date", ">=", $this->date_start)
        ->whereDate("date", "<=", $this->date_end)
        ->get();

        require_once(ROOT."/usr/modules/realization/admin/sub/correct_detail.php");

        foreach($corrections as $correction){
            $item = static::create_item();
            $date = date("d.m.Y", strtotime($correction->date));
            $item->id = $correction->id;
            $item->module_out = "sales";
            $item->pid_out = $correction->realization->sales_id;
            $item->class_name = static::format_module_encode(\Realization\Model\Correct::class);
            $item->module = "correction";
            $item->uid = $correction->uid;
            $item->date = $date;
            $item->org_data->document = "Корректировка продажи ({$correction->numb} от {$date})";
            $item->oid_data->document = "Корректировка прихода ({$correction->numb} от {$date})";
            $item->org_data->document_full = "Корректировка реализации ({$correction->numb} от {$date})";
            $item->oid_data->document_full = "Корректировка продажи ({$correction->numb} от {$date})";

            $flagCorrect = check_correct_mod($correction->toArray());
            $query = query_correct_detail($correction->id);
            $debit = 0;
            while($r = $db->getRow($query)){
                $total = $flagCorrect ? $r["cd2_total"] : $r["rd_total"];
                $debit += $r["total"] - $total;
            }
            $item->debit = $debit;
            $item->link = "/administration/module/realization/correct_detail/{$correction->id}";
            array_push($this->records, $item->complete());
        }
    }

    static function create_item(){
        return new class{
            public $id = null;
            public $module = null;
            public $class_name = null;
            public $uid = null;
            public $date = null;
            public $debit = null;
            public $debit_view = null;
            public $credit = null;
            public $credit_view = null;
            public $link = null;
            public $contract_id = null;
            public $contract_name = null;
            public $module_out = false;
            public $pid_out = false;

            public function __construct(){
                $this->org_data = new \stdClass;
                $this->oid_data = new \stdClass;
                $this->org_data->document = null;
                $this->oid_data->document = null;
                $this->org_data->document_full = null;
                $this->oid_data->document_full = null;
            }

            public function complete(){
                $this->debit_view = \Economic\ActController::price_format($this->debit);
                $this->credit_view = \Economic\ActController::price_format($this->credit);

                $reestr = \Reestr\Models\Reestr::searchByModule($this->module_out, $this->pid_out);
                $this->contract_id = $reestr->id ?? null;
                $this->contract_name = ActController::contract_title($reestr);

                return $this;
            }
        };
    }

    static function contract_title($reestr): string{
        return empty($reestr) ? "Без договора" : "Договор №".(empty($reestr->other_numb) ? $reestr->numb : $reestr->other_numb);
    }
}