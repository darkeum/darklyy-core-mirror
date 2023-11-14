<?php

namespace Organizations\Controller;

use \Boot\Models\Users;
use \Boot\Support\Cache;
use \Boot\Models\UsersOrg;
use Boot\System\Http\Request;
use \Organizations\Grid\IndexGrid;
use Boot\System\Office\OfficeSupport;
use Organizations\Model\Organizations;
use Organizations\OrganizationsParser;
use \Boot\Controller\AbstractController;

class OrganizationsController extends AbstractController{
    static function index(){
        $grid = new IndexGrid;
        display('@organizations/manager/index.html.twig', [
            'grid' =>  $grid->render(),
        ]);
    }

    static function getCacheName($id){
        return "organization_parse.{$id}";
    }

    static function getParseData($id, $organization = false){
        if(!$organization) $organization = Organizations::findOrFail($id);
        $cache_name = static::getCacheName($id);
        if(Cache::has($cache_name)){
            $data = Cache::get($cache_name);
        } else {
            $OrganizationsParser = new OrganizationsParser;
            $OrganizationsParser->init_model($organization);
            $data = $OrganizationsParser->parse();
            Cache::set($cache_name, $data, 86400);
        }
        return $data;
    }

    static private function get_organization_select_user($id = null){
        $oSelectSettingJS = [
            "mod" => "jsReturn",
            "name" => "default_responsible",
            "id" => $id,
            "ajaxUrl" => "getUsersToSelect",
            "return" => true,
            "bootstrap" => true,
        ];
        $oSelectSettingSelect = $oSelectSettingJS;
        $oSelectSettingSelect["mod"] = "select";
        return [
            "js" => oSelectCustom($oSelectSettingJS),
            "select" => oSelectCustom($oSelectSettingSelect),
        ];
    }

    static function show($id){
        $organization = Organizations::findOrFail($id);
        $data = static::getParseData($id, $organization);
        
        $realization = \Realization\Model\Realization::where("oid", $id)->where("active", 1);

        display('@organizations/manager/show/show.html.twig', [
            'id' => $id,
            'organization' => $organization,
            'data' => $data,
            "sales" => [
                "count" => \Sales\Model\Sales::where("oid", $id)->count()
            ],
            "realization" => [
                "count" => $realization->count(),
                "total" => $realization->sum('total'),
            ],
            "finreport" => static::finreport_gen($data->finreport),
        ]);
    }

    static function create(){
        display('@organizations/manager/edit/edit.html.twig', [
            'organization_type' => static::getOrganizationType(),
            'organization_client_type' => Organizations::getClientType(),
            'organization_select_user' => static::get_organization_select_user(),
        ]);
    }

    static function edit($id){
        $organization = Organizations::where("id", $id)->with("users.user")->first();
        display('@organizations/manager/edit/edit.html.twig', [
            'id' => $id,
            'organization' => $organization,
            'organization_select_user' => static::get_organization_select_user($organization->default_responsible),
            'organization_type' => static::getOrganizationType(),
            'organization_client_type' => Organizations::getClientType(),
            'users_org_type' => UsersOrg::getType(),
            'users' => Users::where("active", 1)->where("surname", "!=", "")->get(),
        ]);
    }

    static function store(Request $request){
        $organizations = Organizations::firstOrCreate(["inn" => $request->get("inn")], $request->all());
        header("Location: ".router("admin.organizations.edit", $organizations->id));
    } 

    static function update($id, Request $request){
        $organizations = Organizations::findOrFail($id);
        $data = $request->all();
        foreach($data as $key => $item){
            $data[ $key ] = empty($item) ? null : $item;
        }
        $organizations->update($data);
        echo $organizations->toJson();
    } 

    private static function finreport_gen($finreport){
        $data = cc();
        $fg = function($name, $rus_name) use (&$item, &$data, &$i, &$finreport){
            if(!isset($data->$name)) {
                $data->$name = [];
                $data->$name["name"] = $name;
                $data->$name["rus_name"] = $rus_name;
            } 
            if(isset($item->$name)){
                $data->$name["total"] =   $item->$name;
                $data->$name["year"] =    $item->year;
                if(!empty($finreport[ $i - 1 ]->$name)){
                    $item_prev = $finreport[ $i - 1 ];
                    $data->$name["diff"] = $item->$name - $item_prev->$name;
                    $data->$name["proc"] = $item->$name / $item_prev->$name * 100 - 100;
                }
            }
        };
        for($i = 0; $i <= count($finreport); $i++){
            $item = $finreport[ $i ] ?? cc();
            $fg("balance", "Баланс"); $fg("profit", "Чистая прибыль"); $fg("revenue", "Выручка"); $fg("tax", "Налоги"); $fg("payment", "Взносы");
        }
        return $data;
    }

    static function show_($id){
        dd(static::getParseData($id));
    }

    static function events($id, Request $request){
        $organization = Organizations::findOrFail($id);
        $page = $request->has("page") ? $request->get("page") : 1;
        $OrganizationsParser = new OrganizationsParser;
        $OrganizationsParser->init_model($organization);
        $detail = $OrganizationsParser->getDetails();
        $events = $OrganizationsParser->getCounterPartyUuid($page);
        $events_type = $OrganizationsParser->getEventСategory();
        display('@organizations/manager/events.html.twig', [
            'id' => $id,
            'organization' => $organization,
            'detail' => $detail,
            'events' => $events,
            'events_type' => $events_type,
        ]);
    }

    static function arbitrages($id, Request $request){
        $organization = Organizations::findOrFail($id);
        $page = $request->has("page") ? $request->get("page") : 1;
        $OrganizationsParser = new OrganizationsParser;
        $OrganizationsParser->init_model($organization);
        $arbitrages = $OrganizationsParser->getArbitrageList($page);
        $arbitrages_count = $OrganizationsParser->getArbitrage();
        display('@organizations/manager/arbitrages.html.twig', [
            'id' => $id,
            'organization' => $organization,
            'arbitrages' => $arbitrages,
            'arbitrages_count' => $arbitrages_count,
        ]);
    }

    static function contracts($id, Request $request){
        $organization = Organizations::findOrFail($id);
        $page = $request->has("page") ? $request->get("page") : 1;
        $OrganizationsParser = new OrganizationsParser;
        $OrganizationsParser->init_model($organization);
        $contracts_customer = $OrganizationsParser->getContractsCustomerStat($page);
        $contracts_supplier = $OrganizationsParser->getContractsSupplierStat($page);
        $contracts_total = $OrganizationsParser->getContractsTotal();
        display('@organizations/manager/contracts/contracts.html.twig', [
            'id' => $id,
            'organization' => $organization,
            'contracts_customer' => $contracts_customer,
            'contracts_supplier' => $contracts_supplier,
            'contracts_total' => $contracts_total,
        ]);
    }

    static function fssp($id, Request $request){
        $organization = Organizations::findOrFail($id);
        $page = $request->has("page") ? $request->get("page") : 1;
        $OrganizationsParser = new OrganizationsParser;
        $OrganizationsParser->init_model($organization);
        $analysis = $OrganizationsParser->getAnalysis();
        $fssp = $OrganizationsParser->getFssp();
        display('@organizations/manager/fssp.html.twig', [
            'id' => $id,
            'organization' => $organization,
            'analysis' => $analysis,
            'fssp' => $fssp,
        ]);
    }

    static function relations($id, Request $request){
        $organization = Organizations::findOrFail($id);
        $page = $request->has("page") ? $request->get("page") : 1;
        $OrganizationsParser = new OrganizationsParser;
        $OrganizationsParser->init_model($organization);
        $relations = $OrganizationsParser->getRelation($page);
        $stat_type = $OrganizationsParser->getStatType();
        $status = $OrganizationsParser->getStatus();
        display('@organizations/manager/relations/relations.html.twig', [
            'id' => $id,
            'organization' => $organization,
            'relations' => $relations,
            'stat_type' => $stat_type,
            'status' => $status,
        ]);
    }

    static function clear_cache($id){
        $cache_name = static::getCacheName($id);
        if(Cache::has($cache_name)){
            Cache::delete($cache_name);
        }
        header('Location: '.router('admin.organizations.show', ["id" => $id]));
    }

    static private function getOrganizationType(){
        $ot = Organizations::getType();
        $organization_type = [
            ["key" => "", "value" => "Неизвестно"],
        ];
        foreach($ot as $key => $value){
            $organization_type[] = ["key" => $key, "value" => $value];
        }
        return $organization_type;
    }

    static function editContractsList($id){
        $organization = Organizations::findOrFail($id);
        display('@organizations/manager/edit/edit-contracts-list.html.twig', [
            'organization' => $organization,
        ]);
    } 

    static function findByInn($inn, Request $request){
        $o = Organizations::where("inn", $inn);
        if($request->has("id")){
            $id = $request->get("id");
            $o->where("id", "!=", $id);
        }
        $o = $o->first();
        echo empty($o) ? json_encode([]) : $o->toJson();
    }

    static function requisites($id){
        $organization = Organizations::findOrFail($id);
        $data = static::getParseData($id, $organization);
        display('@organizations/manager/requisites.html.twig', [
            'id' => $id,
            'organization' => $organization,
            'data' => $data,
        ]);
    }

    static function print($id){
        $org = \Organizations\Model\Organizations::find($id);
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        
        $add_table = function($data) use ($section){
            $table = $section->addTable(["borderColor" => "000000", "borderSize" => 1]);
            $cellRowSpan = ['vMerge' => 'restart'];
            foreach($data as $row){
                $table->addRow();
                foreach($row as $cell){
                    $table->addCell($cell["width"] ?? null, $cellRowSpan)->addText($cell["text"]);
                }
            }
        };
        
        $section->addText("Организация {$org->name}", ["bold" => true], ["alignment" => "center"]);

        $width_title = 3000;
        $width_text = 8000;

        $section->addText("Основные данные:");
        $add_table([
            [
                ["text" => "ИНН", "width" => $width_title],
                ["text" => $org->inn, "width" => $width_text],
            ],
            [
                ["text" => "Краткое наименование", "width" => $width_title],
                ["text" => $org->name, "width" => $width_text],
            ],
            [
                ["text" => "Наименование", "width" => $width_title],
                ["text" => $org->name_full, "width" => $width_text],
            ],
            [
                ["text" => "Почтовый адрес", "width" => $width_title],
                ["text" => $org->maddress, "width" => $width_text],
            ],
            [
                ["text" => "КПП", "width" => $width_title],
                ["text" => $org->kpp, "width" => $width_text],
            ],
            [
                ["text" => "ОГРН", "width" => $width_title],
                ["text" => $org->ogrn, "width" => $width_text],
            ],
            [
                ["text" => "Должность руководителя", "width" => $width_title],
                ["text" => $org->post, "width" => $width_text],
            ],
            [
                ["text" => "Руководитель", "width" => $width_title],
                ["text" => $org->director, "width" => $width_text],
            ],
            [
                ["text" => "Действует на основании", "width" => $width_title],
                ["text" => $org->dey, "width" => $width_text],
            ],
        ]);

        $section->addText("\n");
        $section->addText("Контакты:");
        $add_table([
            [
                ["text" => "Телефон/Факс", "width" => $width_title],
                ["text" => $org->phone, "width" => $width_text],
            ],
            [
                ["text" => "E-mail", "width" => $width_title],
                ["text" => $org->email, "width" => $width_text],
            ],
        ]);

        $organizations_bank = $org->banks;
        if($organizations_bank->count() > 0){
            $section->addText("\n");
            $section->addText("Банковские данные:");
            
            foreach($organizations_bank as $item){
                $add_table([
                    [
                        ["text" => "Краткое наименование", "width" => $width_title],
                        ["text" => $item->name_short, "width" => $width_text],
                    ],
                    [
                        ["text" => "Наименование", "width" => $width_title],
                        ["text" => $item->name, "width" => $width_text],
                    ],
                    [
                        ["text" => "Адрес банка", "width" => $width_title],
                        ["text" => $item->address, "width" => $width_text],
                    ],
                    [
                        ["text" => "БИК банка", "width" => $width_title],
                        ["text" => $item->bik, "width" => $width_text],
                    ],
                    [
                        ["text" => "Корр. счет", "width" => $width_title],
                        ["text" => $item->kschet, "width" => $width_text],
                    ],
                    [
                        ["text" => "Расчетный счет", "width" => $width_title],
                        ["text" => $item->rschet, "width" => $width_text],
                    ],
                ]);
                $section->addText("\n");
            }
        }
        

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $filename = "organizations_{$id}.docx";
        $objWriter->save($filename);
        OfficeSupport::get_file($filename);
    }
}