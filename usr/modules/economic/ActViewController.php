<?php

namespace Economic;

use \Boot\Support\Str;

class ActViewController{

    use ActController;

    protected $mp = "М.П.";
    protected $line = "_______________________________________________";
    protected $initial_balance_text = "Сальдо начальное";
    protected $final_balance_text = "Сальдо конечное";
    protected $contract_turnover = "Обороты по договору";

    public function view(){
        global $adminTpl;
        $adminTpl->admin_head();
        // $this->init_data();
        echo <<<HTML
            <div class="panel panel-default mt-20">
                <div class="panel-body">
                    <div><h4>Акт сверки расчетов с контрагентом {$this->numb} от {$this->date_view}</h4></div>
                    <div class="d-flex">
                        <button type="button" class="btn btn-secondary btn-xs" title="Сохранить на диске" 
                        onclick="location.href='/administration/module/economic/act/print/{$this->id}'"><i class="fas fa-save text-primary fa-lg"></i></button>
                    </div>
                </div>
            </div>
            <div class="panel panel-default mt-20">
                <div class="panel-body">
                    <div class="d-flex flex-column align-items-center">{$this->head()}</div>
                    <div class="mt-20">{$this->head_text()}</div>
                    <div class="mt-20">{$this->table()}</div>
                    <div class="mt-20">{$this->footer()}</div>
                </div>
            </div>
HTML;
        $adminTpl->admin_foot();
    }

    public function head(){
        $hl = $this->head_lines();
        
        return <<<HTML
            <h4><b>{$hl[0]}</b></h4>
            <div>{$hl[1]}</div>
            <div>{$hl[2]}</div>
            <div>{$hl[3]}</div>
            <div>{$hl[4]}</div>
HTML;
    }

    public function head_lines(){
        $contract = $this->break_contracts ? "" : "по договору Основной договор";
        return [
            "Акт сверки", 
            "взаимных расчетов за период: {$this->date_start_view} - {$this->date_end_view}",
            "между {$this->org_data->name} (ИНН {$this->org_data->inn})",
            "и {$this->oid_data->name} (ИНН {$this->oid_data->inn})",
            $contract,
        ];
    }

    public function head_text(){
        return "Мы, нижеподписавшиеся, Директор {$this->org_data->name} {$this->org_director_full}, с одной стороны, и {$this->oid_post} {$this->oid_data->name} {$this->oid_data->director}, с другой стороны, составили настоящий акт сверки в том, что состояние взаимных расчетов по данным учета следующее:";
    }

    public function footer(){
        $text = $this->footer_text();
        $text2 = $this->fill_type_value($text);
        $mp = "<div class=\"mt-20\">{$this->mp}</div>";
        return <<<HTML
            <div class="row">
                <div class="col-sm-6"><div>По данным {$this->org_data->name}</div> </div>
                <div class="col-sm-6"><div>По данным {$this->oid_data->name}</div></div>
            </div>
            <div class="row">
                <div class="col-sm-6"><div class="mt-10"><b>{$text}</b></div></div>
                <div class="col-sm-6"><div class="mt-10"><b>{$text2}</b></div></div>
            </div>
            <div class="row">
                <div class="col-sm-6"><div class="mt-20">От {$this->org_data->name}</div></div>
                <div class="col-sm-6"><div class="mt-20">От {$this->oid_data->name}</div></div>
            </div>
            <div class="row">
                <div class="col-sm-6"><div class="mt-20">Директор</div></div>
                <div class="col-sm-6"><div class="mt-20">{$this->oid_post}</div></div>
            </div>
            <div class="row">
                <div class="col-sm-6"><div class="mt-20">{$this->line} ({$this->org_director_short})</div></div>
                <div class="col-sm-6"><div class="mt-20">{$this->line} ({$this->oid_director_short})</div></div>
            </div>
            <div class="row">
                <div class="col-sm-6">{$mp}</div>
                <div class="col-sm-6">{$mp}</div>
            </div>
HTML;
    }

    public function footer_text(){
        $first = "на {$this->date_end_view}";

        if(empty($this->final_balance) || $this->final_balance == 0) return $first." задолженность отсутствует.";
    
        if($this->final_balance > 0){
            $org_name = $this->org_data->name;
            $final_balance = $this->final_balance;
        } else {
            $org_name = $this->oid_data->name;
            $final_balance = abs($this->final_balance);
        }
        $final_balance_view = self::price_format($final_balance);
        $final_balance_rus = self::get_final_balance_rus($final_balance);
        return $first." задолженность в пользу {$org_name} {$final_balance_view} руб. ({$final_balance_rus}).";
    }

    static function get_final_balance_rus($final_balance){
        $final_balance_rus = str_replace("(", "", Str::numbToString($final_balance));
		$final_balance_rus = str_replace(")", "", $final_balance_rus);
        $final_balance_rus = Str::ucfirst($final_balance_rus);
        return $final_balance_rus;
    }

    public function table(){
        $table_records = $this->table_records();
        $this->get_final_balance();
        $tr = [];
        $tr[0] = <<<HTML
            <td style="text-align: center;">Дата</td>
            <td style="text-align: center;">Документ</td>
            <td style="text-align: center;">Дебет</td>
            <td style="text-align: center;">Кредит</td>
HTML;
        $tr[1] = function($text, $debit, $credit){
            $debit = $debit == 0 ? "" : self::price_format($debit);
            $credit = $credit == 0 ? "" : self::price_format($credit);
            return <<<HTML
                <td colspan="2"><b>{$text}</b></td>
                <td style="text-align: end;"><b>{$debit}</b></td>
                <td style="text-align: end;"><b>{$credit}</b></td>
HTML;
        };

        if($this->initial_balance > 0){
            $tr[2] = $tr[1]($this->initial_balance_text, $this->initial_balance, null).$tr[1]($this->initial_balance_text, null, $this->initial_balance);
        } else {
            $abs = abs($this->initial_balance);
            $tr[2] = $tr[1]($this->initial_balance_text, null, $abs).$tr[1]($this->initial_balance_text, $abs, null);
        }

        if($this->final_balance > 0){
            $tr[3] = "{$tr[1]($this->final_balance_text, $this->final_balance, null)}{$tr[1]($this->final_balance_text, null, $this->fill_type_value($this->final_balance))}";
        } else {
            $abs = abs($this->final_balance);
            $tr[3] = $tr[1]($this->final_balance_text, null, $abs).$tr[1]($this->final_balance_text, $this->fill_type_value($abs), null);
        }
        
        return <<<HTML
            <table class="table table-bordered">
                <tr>
                    <td colspan="4" class="col-sm-6">По данным {$this->org_data->name}, руб.</td>
                    <td colspan="4" class="col-sm-6">По данным {$this->oid_data->name}, руб.</td>
                </tr>
                <tr>
                    {$tr[0]}{$tr[0]}
                </tr>
                <tr>
                    {$tr[2]}
                </tr>
                {$table_records}
                <tr>
                    {$tr[1]("Обороты за период", $this->debit_total, $this->credit_total)}{$tr[1]("Обороты за период", $this->fill_type_value($this->credit_total), $this->fill_type_value($this->debit_total))}
                </tr>
                <tr>
                    {$tr[3]}
                </tr>
            </table>
HTML;
    }

    public function fill_type_value($value){
        return $this->fill_type ? null : $value;
    }

    public function table_records(){
        $result = [];
        // $this->get_records();
        $contract_id = -1;
        $next = false;
        $debit_contract = 0;
        $credit_contract = 0;
        foreach($this->records as $key => $item){
            if($this->break_contracts){
                $con_id = $key == 0 ? $item->contract_id : $this->records[ $key - 1 ]->contract_id;
                $initial_contract = $this->initial_balance_contract->{$con_id}->balance;
            }
            if($this->break_contracts && $item->contract_id != $contract_id){
                if($next) $result[] = $this->tre($initial_contract, $debit_contract, $credit_contract);
                $debit_contract = 0;
                $credit_contract = 0;
                $contract_id = $item->contract_id;
                $initial_contract = $this->initial_balance_contract->{$item->contract_id}->balance;
                $next = true;
                $result[] = $this->tr_contract($item, $initial_contract);
            }
            $debit_contract += $item->debit;
            $credit_contract += $item->credit;
            $debit = $item->debit_view;
            $credit = $item->credit_view;
            $result[] = $this->table_records_items($item, $debit, $credit);
            if($this->break_contracts && array_key_last($this->records) == $key){
                $result[] = $this->tre($initial_contract, $debit_contract, $credit_contract);
            } 
        }
        return implode("", $result);
    }

    public function tr($text, $debit, $credit){
        $debit = empty($debit) ? "" : $this->price_format($debit);
        $credit = empty($credit) ? "" : $this->price_format($credit);
        return <<<HTML
            <td colspan="2" style="text-align: end;">{$text}</td>
            <td style="text-align: end;">{$debit}</td>
            <td style="text-align: end;">{$credit}</td>
HTML;
    }

    public function table_records_items($item, $debit, $credit){
        return <<<HTML
        <tr>
            <td>{$item->date}</td>
            <td>{$item->org_data->document}</td>
            <td style="text-align: end;">{$debit}</td>
            <td style="text-align: end;">{$credit}</td>

            <td>{$this->fill_type_value($item->date)}</td>
            <td>{$this->fill_type_value($item->oid_data->document)}</td>
            <td style="text-align: end;">{$this->fill_type_value($credit)}</td>
            <td style="text-align: end;">{$this->fill_type_value($debit)}</td>
        </tr>
HTML;
    }

    public function tr_contract($item, $initial_contract){
        if($initial_contract > 0){
            $tr1 = $this->tr($this->initial_balance_text, $initial_contract, null).$this->tr($this->initial_balance_text, null, $initial_contract);
        } else {
            $abs = abs($initial_contract);
            $tr1 = $this->tr($this->initial_balance_text, null, $abs).$this->tr($this->initial_balance_text, $abs, null);
        }
        return <<<HTML
        <tr><td colspan="8"><span class="ml40">{$item->contract_name}</span></td></tr>
        <tr>{$tr1}</tr>
HTML;
    }

    public function tre($initial_contract, $debit_contract, $credit_contract){
        $result = <<<HTML
        <tr>
            {$this->tr($this->contract_turnover, $debit_contract, $credit_contract)}
            {$this->tr($this->contract_turnover, $this->fill_type_value($credit_contract), $this->fill_type_value($debit_contract))}
        </tr>
HTML;
        $final_balance = $this->calc_final_balance($initial_contract, $debit_contract, $credit_contract);
        if($final_balance > 0){
            return $result.<<<HTML
                <tr>
                    {$this->tr($this->final_balance_text, $final_balance, null)}
                    {$this->tr($this->final_balance_text, $this->fill_type_value(null), $this->fill_type_value($final_balance))}
                </tr>
HTML;
        } else {
            $abs = abs($final_balance);
            return $result.<<<HTML
                <tr>
                    {$this->tr($this->final_balance_text, null, $abs)}
                    {$this->tr($this->final_balance_text, $this->fill_type_value($abs), $this->fill_type_value(null))}
                </tr>
HTML;
        }
    }
}