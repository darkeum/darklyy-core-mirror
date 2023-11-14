<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Home\UI\WEB\Components;

use Boot\Support\Numb;
use Boot\Support\Carbon;
use Bank\Models\BankDetail;
use Supply\Model\Receipts;
use Boot\Support\Facades\Cache;
use Storage\Models\StockTransfer;
use Realization\Model\Realization;
use Boot\System\Livewire\Component;

class Transaction extends Component
{
    public $realizationsCount;
    public $realizationsTotal;
    public $receiptsCount;
    public $receiptsTotal;
    public $transferCount;
    public $transferTotal;
    public $bankInCount;
    public $bankOutCount;
    public $bankInTotal;
    public $bankOutTotal;
    protected $cacheKey = 'home.transaction';

    public function reload()
    {
        Cache::forget($this->cacheKey);
        $this->getTransactionData();
    }
    public function mount()
    {
        $this->getTransactionData();
    }

    protected function getTransactionData()
    {
        $data = Cache::remember($this->cacheKey, 3600, function () {
            $result = [];
            $now = Carbon::now()->format('Y-m-d');
            $start = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
            $end = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');          
            $result['realizationsCount'] =  Realization::where('date', $now)->where('active', 1)->count();
            $result['realizationsTotal'] =  Numb::formatMoney(Realization::where('date', $now)->where('active', 1)->sum('total'), '₽');
            $result['receiptsCount'] =  Receipts::where('date_receipts', $now)->where('active', 1)->count();
            $result['receiptsTotal'] =  Numb::formatMoney(Receipts::where('date_receipts', $now)->where('active', 1)->sum('total') ?: 0, '₽');
            $result['transferCount'] =  StockTransfer::whereBetween('date', [$start, $end])->where('active', 1)->count();
            $transfer =  StockTransfer::whereBetween('date', [$start, $end])->where('active', 1)->withSum('stock_transfer_detail as detail_total', 's_price')->get();
            $transferTotal = 0;
            foreach ($transfer as $value) {
                $transferTotal = $transferTotal + $value->detail_total;
            }
            $result['transferTotal'] = Numb::formatMoney($transferTotal, '₽');
            $result['bankInCount'] =  BankDetail::whereBetween('date', [$start, $end])->where('pay_in', 1)->count();
            $result['bankInTotal'] =  Numb::formatMoney(BankDetail::whereBetween('date', [$start, $end])->where('pay_in', 1)->sum('sum') ?: 0, '₽');
            $result['bankOutCount'] =  BankDetail::whereBetween('date', [$start, $end])->where('pay_in', 0)->count();
            $result['bankOutTotal'] =  Numb::formatMoney(BankDetail::whereBetween('date', [$start, $end])->where('pay_in', 0)->sum('sum') ?: 0, '₽');
            return $result;
        });
        $this->realizationsCount =  $data['realizationsCount'];
        $this->realizationsTotal =  $data['realizationsTotal'];
        $this->receiptsCount =  $data['receiptsCount'];
        $this->receiptsTotal =  $data['receiptsTotal'];
        $this->transferCount =  $data['transferCount'];
        $this->transferTotal = $data['transferTotal'];
        $this->bankInCount = $data['bankInCount'];
        $this->bankInTotal = $data['bankInTotal'];
        $this->bankOutCount = $data['bankOutCount'];
        $this->bankOutTotal = $data['bankOutTotal'];
        return $data;
    }

    public function render()
    {
        return view('home::manager.components.transaction');
    }
}
