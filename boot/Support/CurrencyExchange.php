<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Support;

use SoapClient;
use SimpleXMLElement;
use Boot\Support\Carbon;
use OutOfBoundsException;
use Boot\Support\Facades\Cache;

class CurrencyExchange
{
    /**
     * Список курсов валют
     *
     * @var array
     */
    protected $rates = [];

    /**
     * Конструктор класса
     *
     * @param  string  $date Дата на которую нужно поучить курс, если null то будет выбрана текущая дата
     * @return void
     */
    public function __construct($date = null)
    {
        $this->rates = Cache::remember(!empty($date) ? 'currency_exchange_per_date_' . $date : 'currency_exchange',  Carbon::now()->endOfDay()->addSecond(), function () use ($date) {
            return $this->get_rates($date);
        });
    }

    /**
     * Получение курса валют
     *
     * @param  mixed $date Дата на которую нужно поучить курс, если null то будет выбрана текущая дата
     * @return array
     */
    protected function get_rates($date = null): array
    {
        try {
            return $this->get_soap($date);
        } catch (\Throwable $th) {
            try {
                return $this->get_xml($date);
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }

    /**
     * Получение курса валют через SOAP клиент из ЦБРФ
     *
     * @param  string  $date Дата на которую нужно поучить курс, если null то будет выбрана текущая дата
     * @return array
     */
    protected function get_soap($date = null): array
    {
        $date = empty($date)
            ? date('Y-m-d')
            : date('Y-m-d', strtotime($date));

        $client = new SoapClient("http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL");

        $curs = $client->GetCursOnDate(["On_date" => $date]);
        $rates = new SimpleXMLElement($curs->GetCursOnDateResult->any);
        $pre_rates = [];
        foreach ($rates->ValuteData->ValuteCursOnDate as $rate) {
            $r = (float)$rate->Vcurs / (int)$rate->Vnom;
            $pre_rates['alpha3'][(string)$rate->VchCode] = $r;
            $pre_rates['code'][(int)$rate->Vcode] = $r;
        }
        $pre_rates['alpha3']['RUB'] = 1;
        $pre_rates['code'][643] = 1;
        return $pre_rates;
    }

    /**
     * Получение курса валют через XML клиент из ЦБРФ
     *
     * @param  string  $date Дата на которую нужно поучить курс, если null то будет выбрана текущая дата
     * @return array
     */
    protected function get_xml($date = null): array
    {
        $date = empty($date)
            ? date('d/m/Y')
            : date('d/m/Y', strtotime($date));
        $pre_rates = [];
        $xml = simplexml_load_file('http://www.cbr.ru/scripts/XML_daily.asp?date_req=' . $date);
        foreach ($xml->Valute as $item) {
            $r = (float)$item->Value / (int)$item->Nominal;
            $pre_rates['alpha3'][(string)$item->CharCode] = $r;
            $pre_rates['code'][(int)$item->NumCode] = $r;
        }
        $pre_rates['alpha3']['RUB'] = 1;
        $pre_rates['code'][643] = 1;
        return $pre_rates;
    }

    /**
     * Получение курса указанной валюты к рублю
     *
     * @param  string|int $code Буквенный или цифровой код валюты для получения курса к рублю
     * @return float
     *
     * @throws OutOfBoundsException Если не найден курс по указанному ключу
     */
    public function get($code = ''): float
    {
        $rtn = false;

        if (is_string($code)) {
            $code = strtoupper(trim($code));
            $rtn = isset($this->rates['alpha3'][$code])
                ? $this->rates['alpha3'][$code]
                : false;
        } else if (is_numeric($code)) {
            $rtn = isset($this->rates['code'][$code])
                ? $this->rates['code'][$code]
                : false;
        }
        if ($rtn == false) {
            throw new OutOfBoundsException('Не найден курс по указанному ключу: ' . $code);
        } else {
            return $rtn;
        }
    }

    /**
     * Получение курса между указанными валютами
     *
     * @param  string|int $code_Sell Буквенный или цифровой код валюты для продажи
     * @param  string|int $code_buy Буквенный или цифровой код валюты для покупки
     * @return float
     */
    public function get_cross($code_Sell, $code_buy): float
    {
        return $this->get($code_buy) / $this->get($code_Sell);
    }

    /**
     * Получение массива со всеми курсами валют
     *
     * @return array
     */
    public function all(): array
    {
        return $this->rates;
    }
}
