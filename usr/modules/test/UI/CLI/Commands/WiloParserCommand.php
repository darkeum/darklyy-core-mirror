<?php

namespace Test\UI\CLI\Commands;

use \Boot\System\Core\Abstracts\Commands\ConsoleCommand;
use Exception;

class WiloParserCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'parser:wilo';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Парсер Wilo';

    protected $csrfToken;
    protected $client;

    static function getCsrfToken($html){
        preg_match('/csrfToken = "(.*)";/', $html, $matches);
        return $matches[1];
    }

    static function getTimeStamp(){
        return date("j").round(microtime(true) * 1000);
    }

    static function getDn($value){
		$va = explode(',', $value);
		switch ($va[0]) {
			default:
				$vaa = explode(' ', $va[0]);
                return $vaa[0];
			case "G 1":
                return 15;
			case "G 1¼":
                return 20;
			case "G 1½":
                return 25;
			case "G 2":
                return 32;
		}
	}

    static function getType($p){
        switch($p->NAME){
            default:
                throw new \Exception("Неизвестный тип: {$p->NAME}");
            case "HEAD":
                return "QH";
            case "NPSH":
                return "NPSH";
            case "ETAHYDR":
            case "ETATOT":
                return "QE";
            case "POWERP2":
                return "QP2";
            case "POWERP1":
                return "QP1";
        }
    }

    static function skipMessage($catalog_item, $message){
        dump("Skip cid: {$catalog_item->id}, cause: `{$message}`;");
    }

    public function ResultImageHandler(){
        $TimeStamp = $this->getTimeStamp();
        $this->client->get("https://ru.wilo-select.com/ResultImageHandler.vhd?RequestedHandler=ResultImageHandler&IMGBGCOLOR=e9e9e9&TimeStamp={$TimeStamp}&IMGWIDTH=225&IMGHEIGHT=51");
    }

    public function PumpCurveValueHandler(){
        return json_decode($this->client->post("https://ru.wilo-select.com/PumpCurveValueHandler.vhd", [
            "form_params" => [
                "RequestedHandler" => "PumpCurveValueHandler",
                "__csrfToken" => $this->csrfToken,
                "action" => "GETPOINTS",
            ],
        ])->getBody()->getContents());
    }

    public function PumpCurveOpHandler(){
        $this->client->post("https://ru.wilo-select.com/PumpCurveOpHandler.vhd", [
            "form_params" => [
                "RequestedHandler" => "PumpCurveOpHandler",
                "__csrfToken" => $this->csrfToken,
                "action" => "get",
            ],
        ]);
    }

    public function PumpVFDHandler(){
        $this->client->post("https://ru.wilo-select.com/PumpVFDHandler.vhd", [
            "form_params" => [
                "RequestedHandler" => "PumpVFDHandler",
                "__csrfToken" => $this->csrfToken,
                "action" => "get",
            ],
        ]);
    }

    public function getPoints(){
        $client = $this->client;
        $this->ResultImageHandler();
        $client->get("https://ru.wilo-select.com/DBImageHandler.vhd?RequestedHandler=DBImageHandler&imgFileName=smAddSysCurves");
        $this->ResultImageHandler();

        $pump_points = $this->PumpCurveValueHandler();
        
        $this->PumpCurveOpHandler();
        $this->PumpVFDHandler();

        return $pump_points;
    }

    public function parse_select_option($html){
        $dom = new \PHPHtmlParser\Dom;
        $dom->loadStr($html);
        $options = $dom->find('option');
        $data = [];
        foreach($options as $option){
            $data[] = (object)[
                "text" => $option->text,
                "value" => $option->value,
                "selected" => $option->hasAttribute("selected"),
            ];
        }
        return $data;
    }

    public function getDataPump(){
        return json_decode($this->client->post("https://ru.wilo-select.com/PumpConfigResultHandler.vhd", [
            "form_params" => [
                "RequestedHandler" => "PumpConfigResultHandler",
                "__csrfToken" => $this->csrfToken,
                "action" => "GET",
            ],
        ])->getBody()->getContents());
    }

    public function ResultNaviHandler(){
        $this->client->post("https://ru.wilo-select.com/ResultNaviHandler.vhd", [
            "form_params" => [
                "RequestedHandler" => "ResultNaviHandler",
                "__csrfToken" => $this->csrfToken,
                "action" => "GET",
            ],
        ]);
    }

    public function ResultDiagramMenuHandler(){
        $this->client->post("https://ru.wilo-select.com/ResultDiagramMenuHandler.vhd", [
            "form_params" => [
                "RequestedHandler" => "ResultDiagramMenuHandler",
                "__csrfToken" => $this->csrfToken,
                "action" => "GET",
            ],
        ]);
    }

    public function ResultOptimizeHandler(){
        $this->client->post("https://ru.wilo-select.com/ResultOptimizeHandler.vhd", [
            "form_params" => [
                "RequestedHandler" => "ResultOptimizeHandler",
                "__csrfToken" => $this->csrfToken,
                "action" => "GETMETHODS",
            ],
        ]);
    }

    public function PumpDiagramInfoHandler(){
        $this->client->post("https://ru.wilo-select.com/PumpDiagramInfoHandler.vhd", [
            "form_params" => [
                "RequestedHandler" => "PumpDiagramInfoHandler",
                "__csrfToken" => $this->csrfToken,
                "action" => "GETINFOS",
            ],
        ]);
    }

    public function DutyPointHandler(){
        $this->client->post("https://ru.wilo-select.com/DutyPointHandler.vhd", [
            "form_params" => [
                "RequestedHandler" => "DutyPointHandler",
                "__csrfToken" => $this->csrfToken,
                "ident" => "FROMPIPECALC",
                "TimeStamp" => $this->getTimeStamp(),
            ],
        ]);
    }

    public function SelectPumpHandler($xid){
        $this->client->post("https://ru.wilo-select.com/SelectPumpHandler.vhd", [
            "form_params" => [
                "RequestedHandler" => "SelectPumpHandler",
                "__csrfToken" => $this->csrfToken,
                "action" => "selectPump",
                "xid" => $xid,
            ],
        ]);
    }

    public function PumpCfgTableHandler(){
        #Pump table
        return json_decode($this->client->post("https://ru.wilo-select.com/PumpCfgTableHandler.vhd", [
            "form_params" => [
                "RequestedHandler" => "PumpCfgTableHandler",
                "__csrfToken" => $this->csrfToken,
                "Option" => "GETTREE",
                "Timestamp" => $this->getTimeStamp(),
                "canedit" => "true",
            ],
        ])->getBody()->getContents());
    }

    public function parsePoints(&$points_data, $points, $type_of_instalation, $work_line, $priority){
        foreach($points->POINTS as $p){
            $type = $this->getType($p);
            $data_point = [];
            foreach($p->VALUES as $pp){
                $data_point[] = [
                    "Q" => $pp[0],
                    "H" => $pp[1],
                ];
            }
            $points_data[] = (object)[
                "points" => $data_point,
                "type" => $type,
                "type_of_instalation" => $type_of_instalation,
                "work_line" => $work_line,
                "priority" => $priority,
            ];
            // dump("Add cid: {$catalog_item->id}, type_of_instalation: {$type_of_instalation}; work_line: {$work_line}; type: {$type}; priority: {$priority};");
        }
    }

    public function takePump($catalog_item){
        $client = $this->client;
        $this->csrfToken = $this->getCsrfToken($client->get('https://ru.wilo-select.com/StartMain.aspx')->getBody()->getContents());
        $res = $client->post("https://ru.wilo-select.com/StateMachineHandler.vhd", [
            "form_params" => [
                "RequestedHandler" => "StateMachineHandler",
                "__csrfToken" => $this->csrfToken,
                "action" => "Trigger",
                "Trigger" => "7E8CC2C0-A0ED-41b1-8934-43D04BDAE82B",
            ],
        ]);
        $this->csrfToken = $this->getCsrfToken($client->get('https://ru.wilo-select.com/ProductSearch.aspx')->getBody()->getContents());
        $res = $client->post("https://ru.wilo-select.com/ProductSearchServerHandler.vhd", [
            "form_params" => [
                "RequestedHandler" => "ProductSearchServerHandler",
                "__csrfToken" => $this->csrfToken,
                "action" => "ProductSearch.QueryInitialValues",
            ]
        ]);
        #find pumps
        $res = $client->post("https://ru.wilo-select.com/ProductSearchServerHandler.vhd", [
            "form_params" => [
                "RequestedHandler" => "ProductSearchServerHandler",
                "__csrfToken" => $this->csrfToken,
                "action" => "ProductSearch.SetSearchValue",
                "searchValue" => $catalog_item->sku,
            ],
        ]);
        while(true){
            $data = json_decode($client->post("https://ru.wilo-select.com/ProductSearchServerHandler.vhd", [
                "form_params" => [
                    "RequestedHandler" => "ProductSearchServerHandler",
                    "__csrfToken" => $this->csrfToken,
                    "action" => "ProductSearch.QuerySearchResults",
                ],
            ])->getBody()->getContents());
            if(!$data->statusUpate->isExecutingSearch){
                break;
            }
        }
        // dd(json_decode($res->getBody()->getContents()));

        $pumps_length = count($data->searchResultsUpdate);
        if($pumps_length == 1){
            $pump = $data->searchResultsUpdate[0];
        
            $client->get("https://ru.wilo-select.com/ProductSearch_Wait.aspx");
            #select pump
            $res = $client->post("https://ru.wilo-select.com/ProductSearchServerHandler.vhd", [
                "form_params" => [
                    "RequestedHandler" => "ProductSearchServerHandler",
                    "__csrfToken" => $this->csrfToken,
                    "action" => "ProductSearch.SelectSearchResult",
                    "objRef" => $pump->objRef,
                    "itemNumber" => $pump->itemNumber,
                    "description" => $pump->description,
                ],
            ]);
            $client->get("https://ru.wilo-select.com/ProductSearch_Wait.aspx");
            $res = $client->post("https://ru.wilo-select.com/ProductSearchServerHandler.vhd", [
                "form_params" => [
                    "RequestedHandler" => "ProductSearchServerHandler",
                    "__csrfToken" => $this->csrfToken,
                    "action" => "ProductSearch.QueryDetailAvailability",
                ],
            ]);
            $this->csrfToken = $this->getCsrfToken($client->get('https://ru.wilo-select.com/ProductSearch_PumpDetails.aspx')->getBody()->getContents());
            
            //???
            $res = $client->post("https://ru.wilo-select.com/DutyPointHandler.vhd", [
                "form_params" => [
                    "RequestedHandler" => "DutyPointHandler",
                    "__csrfToken" => $this->csrfToken,
                    "ident" => "GETTREE",
                ],
            ]);

            $res = $client->post("https://ru.wilo-select.com/StateMachineHandler.vhd", [
                "form_params" => [
                    "RequestedHandler" => "StateMachineHandler",
                    "__csrfToken" => $this->csrfToken,
                    "action" => "CurrentState",
                ],
            ]);
            #Data pump
            $data_pump = $this->getDataPump();
            #???
            $this->DutyPointHandler();
            #Select pump
            $this->SelectPumpHandler($data_pump->data[0]->xID);
            
            $pump_table = $this->PumpCfgTableHandler();
            // dd($pump_table);
            foreach($pump_table->Rows as $pt){
                if(count($pt->Cells) > 1){
                    $cells = $pt->Cells;
                    switch($cells[0]->InnerHTML){
                        // case "Рабочее поле":
                        //     static::skipMessage($catalog_item, "Рабочее поле");
                        //     return;
                        // break;
                        case "Тип установки":
                            $is_double_pump = true;
                            if(preg_match("/<select .*/", $cells[1]->InnerHTML) == 1){
                                $double_pump = $this->parse_select_option($cells[1]->InnerHTML);
                            }
                        break;
                        case "Рабочая линия":
                            $working_line = $this->parse_select_option($cells[1]->InnerHTML);
                        break;
                    }
                }
            }
            $this->ResultNaviHandler();
            $this->ResultDiagramMenuHandler();
            $this->ResultOptimizeHandler();
            $this->PumpDiagramInfoHandler();
            #получение точек
            $points = $this->getPoints();
            if(!isset($points->POINTS)){
                #Показать точки кривой
                $client->post("https://ru.wilo-select.com/ResultDiagramMenuHandler.vhd", [
                    "form_params" => [
                        "RequestedHandler" => "ResultDiagramMenuHandler",
                        "__csrfToken" => $this->csrfToken,
                        "action" => "SET",
                        "ident" => "8272",
                        "pgName" => "DIAGRAM",
                    ],
                ]);
                #получение точек
                $points = $this->getPoints();
            }
            $type_of_instalation = 0;
            $work_line = 1;
            $priority = 1;
            // dd($points);
            $points_data = [];
            $this->parsePoints($points_data, $points, $type_of_instalation, $work_line, $priority);
        }

        return (object)[
            "pumps_length" => $pumps_length,
            "is_double_pump" => $is_double_pump ?? false,
            "double_pump" => $double_pump ?? [],
            "working_line" => $working_line ?? [],
            "points_data" => $points_data ?? [],
        ];
    }

    public function changeWorkLine($value){
        $this->client->post("https://ru.wilo-select.com/PumpCfgTableHandler.vhd", [
            "form_params" => [
                "RequestedHandler" => "PumpCfgTableHandler",
                "__csrfToken" => $this->csrfToken,
                "Option" => "SETTREEVAL",
                "TableID" => "PumpCfgTable_table",
                "RowIdx" => "3",
                "ColIdx" => "1",
                "Value" => $value,
                "Timestamp" => $this->getTimeStamp(),
                "canedit" => "true",
            ],
        ]);
    }

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->client = new \GuzzleHttp\Client([
            'verify'  => false,
			'allow_redirects' => true,
			'cookies' => true,
        ]);

        #Select region
    //    $csrfToken = $this->getCsrfToken($client->get("https://ru.wilo-select.com/Region.aspx")->getBody()->getContents());
    //    $client->post("https://ru.wilo-select.com/WiloRegionSelector.vhd", [
    //        "form_params" => [
    //            "RequestedHandler" => "WiloRegionSelector",
    //            "__csrfToken" => $csrfToken,
    //            "action" => "SelectRegion",
    //            "selectedRegion" => "NTIyNWJjMjgtMWRmYy00Mzc4LTgxMDEtZGFlZjg3YjUxOTEy",
    //        ]
    //     ]);
        $query = \Catalog_v2\Model\Catalog::where("man", 20)->where("hidden", 0)->where("category", ",363,")->orderBy("id", "DESC");
        $count = $query->count();
        dump("Всего товаров: {$count}");
        $i = 0;
        foreach($query->lazy() as $catalog_item){
            $i++;
            // while(true){
            //     try {
            //         $data = $this->takePump($catalog_item);
            //         break;
            //     } catch (\Exception $e) {
            //         dump($e->getMessage());
            //     }
            // }

            try {
                $data = $this->takePump($catalog_item);
            } catch (\Exception $e) {
                dump("id: {$catalog_item->id}, error: {$e->getMessage()}");
                continue;
            }
            
            if($data->pumps_length == 1){
                #переключение на другую рабочую линию
                if(count($data->working_line) > 0){
                    foreach($data->working_line as $item){
                        if($item->selected == false){
                            $this->changeWorkLine($item->value);
                            $points = $this->getPoints();
                            $this->parsePoints($data->points_data, $points, 1, $item->value, 1);
                        }
                    }
                }
                #переключение на резервный
                if($data->is_double_pump && count($data->double_pump) > 0){
                    $this->client->post("https://ru.wilo-select.com/PumpCfgTableHandler.vhd", [
                        "form_params" => [
                            "RequestedHandler" => "PumpCfgTableHandler",
                            "__csrfToken" => $this->csrfToken,
                            "Option" => "SETTREEVAL",
                            "TableID" => "PumpCfgTable_table",
                            "RowIdx" => "1",
                            "ColIdx" => "1",
                            "Value" => $data->double_pump[1]->value,
                            "Timestamp" => $this->getTimeStamp(),
                            "canedit" => "true",
                        ],
                    ]);
                    if(count($data->working_line) > 0){
                        foreach($data->working_line as $item){
                            $this->changeWorkLine($item->value);
                            $points = $this->getPoints();
                            $this->parsePoints($data->points_data, $points, 2, $item->value, 1);
                        }
                    } else {
                        $points = $this->getPoints();
                        $this->parsePoints($data->points_data, $points, 2, 1, 1);
                    }
                }
                dump("Насос {$i} из {$count}. ID: {$catalog_item->id}, sku: {$catalog_item->sku}, count: ".count($data->points_data));
            } else {
                if($data->pumps_length == 0){
                    static::skipMessage($catalog_item, "Насос не найден");
                } else {
                    static::skipMessage($catalog_item, "Насос найден не один");
                }
            }
        }
    }
}
