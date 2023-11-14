<?php

namespace Organizations;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Organizations\Model\Organizations;
use Carbon\Carbon;

class OrganizationsParser{

    private $JWTKey;
    public $client;
    public $inn;
    public $ogrn;
    private $scheme = "https";
    private $host = "vbankcenter.ru";

    public function __construct(){
        $this->data = cc();
        $this->data->parse_date = Carbon::now();
        $this->getJWTKey();
    }

    public function init_id($id){
        $this->init_model(Organizations::findOrFail($id));
    }

    public function init_model($model){
        $this->inn = trim($model->inn);
        $this->ogrn = trim($model->ogrn);
    }

    public function parse(){
        if(empty($this->inn)) throw new \Exception('`inn` не указано');
        if(empty($this->ogrn)) throw new \Exception('`ogrn` не указано');

        $this->getDetails();
        $this->client = new Client();
        $this->data->rsmp = $this->getRSMP();

        $promises = [
            "finreport" => $this->getFinreportAsync(),
            "events" => $this->getCounterPartyUuidAsync(),
            "events_type" => $this->getEventСategoryAsync(),
            "kind_activity" => $this->getKindActivityAsync(),
            "contacts" => $this->getContactsAsync(),
            "arbitrage_count" => $this->getArbitrageAsync(),
            "arbitrage" => $this->getArbitrageListAsync(),
            "contracts_customer" => $this->getContractsCustomerStatAsync(),
            "contracts_supplier" => $this->getContractsSupplierStatAsync(),
            "contracts_total" => $this->getContractsTotalAsync(),
            "analysis" => $this->getAnalysisAsync(),
            "stat" => $this->getStatAsync(),
            "stat_type" => $this->getStatTypeAsync(),
        ];

        $responses = Promise\Utils::settle($promises)->wait();
        foreach($responses as $key => $response){
            $this->data->$key = json_decode($response["value"]->getBody());
        }

        return $this->data;
    }

    public function getJWTKey(){
        $client = new Client();
        $res = $client->request('GET', $this->url("/contragent"));
        $page = $res->getBody();
        preg_match('|<script id="gweb-app-state" type="application/json">(.*)</script>|Uis', $page, $matches);
        if(empty($matches[1])) throw new \Exception('`script` не найден');
        // preg_match('|JWT_KEY&q;:&q;(.*)&q;|Uis', $matches[1], $matches2);
        // if(empty($matches2[1])) throw new \Exception('`JWT_KEY` не найден');
        // $this->JWTKey = $matches2[1];
        $this->JWTKey = null;
        return $this->JWTKey;
    }

    public function get($url){
        $client = new Client();
        $res = $client->request('GET', $url, [
            'headers' => [
                'x-jwt' => $this->JWTKey,
            ],
        ]);
        return json_decode($res->getBody());
    }

    public function getAsync($url){
        return $this->client->requestAsync('GET', $url, [
            'headers' => [
                'x-jwt' => $this->JWTKey,
            ],
        ]);
    }

    public function url($url){
        return "{$this->scheme}://{$this->host}{$url}";
    }

    /* Parse data */

    //общие данные
    public function getDetails(){
        $detail = $this->get($this->url("/contragent/api/web/counterparty/ogrn/{$this->ogrn}/details?withDescription=true"));
        $status = $this->getStatus();
        if(isset($detail->partnerStatus)){
            $detail->status_view = $status[ $detail->partnerStatus ] ?? $detail->partnerStatus;
        }
        $employeeCountByYears = [];
        if(!empty($detail->employeeCountByYears)){
            foreach($detail->employeeCountByYears as $key => $value){
                $s = new \stdClass;
                $s->count = $value;
                $s->year = $key;
                $employeeCountByYears[] = $s;
            }
        }
        $detail->employeeCountByYears = $employeeCountByYears;
        $this->data->detail = $detail;
        return $this->data->detail;
    }

    //список статусов организации (действует, не действует...)
    public function getStatus(){
        // return $this->get($this->url("/contragent/api/web/counterparty/license/status"));
        return [
            "ACTIVE" => "ДЕЙСТВУЕТ",
            "TERMINATED" => "НЕ ДЕЙСТВУЕТ",
        ];
    }

    //источники проверок
    public function getSourceUrl(){
        return $this->url("/contragent/api/web/counterparty/license/source");
    }
    public function getSource(){
        return $this->get($this->getSourceUrl());
    }
    public function getSourceAsync(){
        return $this->getAsync($this->getSourceUrl());
    }

    //список контактов
    public function getContactsUrl(){
        return $this->url("/contragent/api/web/counterparty/ogrn/{$this->ogrn}/contacts/list");
    }
    public function getContacts(){
        return $this->get($this->getContactsUrl());
    }
    public function getContactsAsync(){
        return $this->getAsync($this->getContactsUrl());
    }

    //Арбитражные дела (количество)
    public function getArbitrageUrl(){
        return $this->url("/contragent/api/web/counterparty/arbitrage/{$this->ogrn}");
    }
    public function getArbitrage(){
        return $this->get($this->getArbitrageUrl());
    }
    public function getArbitrageAsync(){
        return $this->getAsync($this->getArbitrageUrl());
    }

    //список Арбитражные дел
    public function getArbitrageListUrl($page){
        $page--;
        return $this->url("/contragent/api/web/counterparty/arbitrage?partnerOgrn={$this->ogrn}&page={$page}");
    }
    public function getArbitrageList($page = 1){
        return $this->get($this->getArbitrageListUrl($page));
    }
    public function getArbitrageListAsync($page = 1){
        return $this->getAsync($this->getArbitrageListUrl($page));
    }

    //рейтинг
    public function getStatisticUrl(){
        return $this->url("/contragent/api/web/counterparty/review/statistic/ogrn/{$this->ogrn}");
    }
    public function getStatistic(){
        return $this->get($this->getStatisticUrl());
    }
    public function getStatisticAsync(){
        return $this->getAsync($this->getStatisticUrl());
    }

    //баланс по годам
    public function getFinreportUrl(){
        return $this->url("/contragent/api/web/counterparty/inn/{$this->inn}/finreport");
    }
    public function getFinreport(){
        return $this->get($this->getFinreportUrl());
    }
    public function getFinreportAsync(){
        return $this->getAsync($this->getFinreportUrl());
    }

    //список событий
    public function getCounterPartyUuidUrl($page){
        $page--;
        return $this->url("/events/api/web/event/counterparty-uuid/{$this->data->detail->partnerUuid}?page={$page}");
    }
    public function getCounterPartyUuid($page = 1){
        return $this->get($this->getCounterPartyUuidUrl($page));
    }
    public function getCounterPartyUuidAsync($page = 1){
        return $this->getAsync($this->getCounterPartyUuidUrl($page));
    }

    //список вид деятельности
    public function getKindActivityUrl(){
        switch($this->data->detail->type){
            default:
                $type = "legal";
            break;
            case "SELF_EMPLOYED":
                $type = "selfEmpl";
            break;
        }
        return $this->url("/contragent/api/web/counterparty/type/{$type}/uuid/{$this->data->detail->partnerUuid}/activityTypes");
    }
    public function getKindActivity(){
        return $this->get($this->getKindActivityUrl());
    }
    public function getKindActivityAsync(){
        return $this->getAsync($this->getKindActivityUrl());
    }

    //список значений событий
    public function getEventСategoryUrl(){
        return $this->url("/events/api/web/event/category");
    }
    public function getEventСategory(){
        return $this->get($this->getEventСategoryUrl());
    }
    public function getEventСategoryAsync(){
        return $this->getAsync($this->getEventСategoryUrl());
    }

    //список типа предприятия
    public function getRSMP(){
        return [
            "MICRO" => "Микропредприятие",
            "SMALL" => "Малое предприятие",
            "MIDDLE" => "Среднее предприятие",
        ];
    }

    //список Госконтрактов как заказчик
    public function getContractsCustomerStatUrl($page){
        $page--;
        return $this->url("/contragent/api/web/counterparty/ogrn/{$this->ogrn}/contracts/customer/stat?inn={$this->inn}&page={$page}");
    }
    public function getContractsCustomerStat($page = 1){
        return $this->get($this->getContractsCustomerStatUrl($page));
    }
    public function getContractsCustomerStatAsync($page = 1){
        return $this->getAsync($this->getContractsCustomerStatUrl($page));
    }

    //список Госконтрактов как поставщик
    public function getContractsSupplierStatUrl($page){
        $page--;
        return $this->url("/contragent/api/web/counterparty/ogrn/{$this->ogrn}/contracts/supplier/stat?inn={$this->inn}&page={$page}");
    }
    public function getContractsSupplierStat($page = 1){
        return $this->get($this->getContractsSupplierStatUrl($page));
    }
    public function getContractsSupplierStatAsync($page = 1){
        return $this->getAsync($this->getContractsSupplierStatUrl($page));
    }

    //общее количество и сумма Госконтрактов(заказчик и поставщик)
    public function getContractsTotalUrl(){
        return $this->url("/contragent/api/web/counterparty/ogrn/{$this->ogrn}/contracts/total?inn={$this->inn}");
    }
    public function getContractsTotal(){
        return $this->get($this->getContractsTotalUrl());
    }
    public function getContractsTotalAsync(){
        return $this->getAsync($this->getContractsTotalUrl());
    }

    //Проверки и Жалобы ФАС и Исполнительные производства
    public function getAnalysisUrl(){
        return $this->url("/contragent/api/web/counterparty/ogrn/{$this->ogrn}/analysis?inn={$this->inn}");
    }
    public function getAnalysis(){
        return $this->get($this->getAnalysisUrl());
    }
    public function getAnalysisAsync(){
        return $this->getAsync($this->getAnalysisUrl());
    }

    //Связи количества
    public function getStatUrl(){
        return $this->url("/contragent/api/web/counterparty/relation/ogrn/{$this->ogrn}/stat");
    }
    public function getStat(){
        return $this->get($this->getStatUrl());
    }
    public function getStatAsync(){
        return $this->getAsync($this->getStatUrl());
    }

    //Связи
    public function getRelationUrl($page){
        $page--;
        return $this->url("/contragent/api/web/counterparty/relation?page={$page}&ogrn={$this->ogrn}");
    }
    public function getRelation($page = 1){
        return $this->get($this->getRelationUrl($page));
    }
    public function getRelationAsync($page = 1){
        return $this->getAsync($this->getRelationUrl($page));
    }

    //список типы связей
    public function getStatTypeUrl(){
        return $this->url("/contragent/api/web/counterparty/relation/category");
    }
    public function getStatType(){
        return $this->get($this->getStatTypeUrl());
    }
    public function getStatTypeAsync(){
        return $this->getAsync($this->getStatTypeUrl());
    }

    //список Исполнительные производства
    public function getFsspUrl($page){
        $page--;
        return $this->url("/contragent/api/web/counterparty/fssp?page={$page}&ogrn={$this->ogrn}&inn={$this->inn}");
    }
    public function getFssp($page = 1){
        return $this->get($this->getFsspUrl($page));
    }
    public function getFsspAsync($page = 1){
        return $this->getAsync($this->getFsspUrl($page));
    }

    //???
    public function getRnpRecordsUrl(){
        return $this->url("/contragent/api/web/counterparty/inn/{$this->inn}/rnpRecords");
    }
    public function getRnpRecords(){
        return $this->get($this->getRnpRecordsUrl());
    }
    public function getRnpRecordsAsync(){
        return $this->getAsync($this->getRnpRecordsUrl());
    }

    //???
    public function getCounterPartyRevenueUrl($year){
        return $this->url("/contragent/api/web/counterparty/revenue/like/year/{$year}/inn/{$this->inn}");
    }
    public function getCounterPartyRevenue($year){
        return $this->get($this->getCounterPartyRevenueUrl($year));
    }
    public function getCounterPartyRevenueAsync($year){
        return $this->getAsync($this->getCounterPartyRevenueUrl($year));
    }

    //???
    public function getCounterPartyBalanceUrl($year){
        return $this->url("/contragent/api/web/counterparty/balance/like/year/{$year}/inn/{$this->inn}");
    }
    public function getCounterPartyBalance($year){
        return $this->get($this->getCounterPartyBalanceUrl($year));
    }
    public function getCounterPartyBalanceAsync($year){
        return $this->getAsync($this->getCounterPartyBalanceUrl($year));
    }
}