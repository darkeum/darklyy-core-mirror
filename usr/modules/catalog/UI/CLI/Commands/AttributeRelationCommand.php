<?php

namespace Catalog\UI\CLI\Commands;

use \Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class AttributeRelationCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'attribute:relation:0';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Attribute Relation';

    protected $start = 0;
    protected $end = 0;
    protected $file_num = 0;

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */

    public function query(){
        return \Catalog_v2\Model\Catalog::whereNotNull("attribute")->where("id", ">", $this->start)->where("id", "<=", $this->end);
    }

    public function isJson($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public function error_write($fp, $item, $attr_id, $attr_value){
        $text = "Не найден атрибут cid:{$item->id} {$attr_id}:{$attr_value}";
        dump($text);
        fwrite($fp, $text . PHP_EOL);
    }

    public function handle()
    {
        $attribute = \Catalog\Model\CatalogAttribute::all();
        $catalog_attribute_value = \Catalog\Model\CatalogAttributeValue::all();
        $fp = fopen("file_{$this->file_num}.txt", 'a');
        foreach($this->query()->lazy() as $item){
            dump($item->id);
            if(!empty($item->attribute) && gettype($item->attribute) == "array"){
                foreach($item->attribute as $attr_id => $attr_value){
                    if(isset($new_attr_id)) unset($new_attr_id);

                    if(!empty($attr_value) && 
                    \Catalog\Model\CatalogAttributeCategory::where("category_id", str_replace(",", "", $item->category))->
                    where("attribute_id", $attr_id)->exists()){
                        $attr = $attribute->find($attr_id);
                        if($attr->type == "selection"){
                            $cav = $catalog_attribute_value->where("attribute_id", $attr_id)->where("value", $attr_value)->first();
                            if(empty($cav)) {
                                switch($attr_id){
                                    default:
                                        $this->error_write($fp, $item, $attr_id, $attr_value);
                                    break;
                                    case 180:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "12-13,2":
                                                $new_attr_id = 1317;
                                            break;
                                            case "10.5 – 42.4":
                                                $new_attr_id = 2315;
                                            break;
                                            case "3,0-5,5":
                                                $new_attr_id = 2316;
                                            break;
                                            case "24-Dec":
                                                $new_attr_id = 1311;
                                            break;
                                        }
                                    break;
                                    case 1:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "SOLAR HT":
                                            case "SOLAR HT AL CLAD":
                                                $new_attr_id = 1;
                                            break;
                                        }
                                    break;
                                    case 201:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "А":
                                                $new_attr_id = 1413;
                                            break;
                                            case "АА":
                                                $new_attr_id = 1414;
                                            break;
                                            case "В":
                                                $new_attr_id = 1415;
                                            break;
                                            case "С":
                                                $new_attr_id = 1416;
                                            break;
                                            case "СС":
                                                $new_attr_id = 1417;
                                            break;
                                            case "Е":
                                                $new_attr_id = 1419;
                                            break;
                                            case "ЕЕ":
                                                $new_attr_id = 1420;
                                            break;
                                        }
                                    break;
                                    case 245:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "нет":
                                                $new_attr_id = 1576;
                                            break;
                                        }
                                    break;
                                    case 10:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "Одинарный":
                                            case "Сдвоенный":
                                                $new_attr_id = null;
                                            break;
                                            case "Муфта":
                                            case "Муфтовый":
                                            case "муфтовый":
                                                $new_attr_id = 93;
                                            break;
                                            case "Фланец":
                                            case "Фланцевый":
                                                $new_attr_id = 87;
                                            break;
                                        }
                                    break;
                                    case 103:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "80":
                                            case "90":
                                                $new_attr_id = 656;
                                            break;
                                        }
                                    break;
                                    case 110:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "1,5":
                                                $new_attr_id = 2061;
                                            break;
                                            case "2,0":
                                                $new_attr_id = 689;
                                            break;
                                            case "2,3":
                                                $new_attr_id = 2062;
                                            break;
                                            case "3,0":
                                                $new_attr_id = 693;
                                            break;
                                            case "3,3":
                                                $new_attr_id = 2611;
                                            break;
                                            case "3,5":
                                                $new_attr_id = 695;
                                            break;
                                            case "4,0":
                                                $new_attr_id = 697;
                                            break;
                                            case "4,5":
                                                $new_attr_id = 698;
                                            break;
                                            case "5,0":
                                                $new_attr_id = 699;
                                            break;
                                            case "6,0":
                                                $new_attr_id = 701;
                                            break;
                                            case "9,0":
                                                $new_attr_id = 704;
                                            break;
                                        }
                                    break;
                                    case 113:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "129":
                                                $new_attr_id = 2069;
                                            break;
                                            case "15":
                                                $new_attr_id = 2071;
                                            break;
                                            case "154":
                                                $new_attr_id = 2072;
                                            break;
                                            case "26-Sep":
                                                $new_attr_id = 2075;
                                            break;
                                            case "368":
                                                $new_attr_id = 2077;
                                            break;
                                            case "377":
                                                $new_attr_id = 2078;
                                            break;
                                            case "426":
                                                $new_attr_id = 2080;
                                            break;
                                            case "50":
                                                $new_attr_id = 2081;
                                            break;
                                            case "530":
                                                $new_attr_id = 2082;
                                            break;
                                            case "65":
                                                $new_attr_id = 2084;
                                            break;
                                        }
                                    break;
                                    case 114:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "ВН/НР":
                                                $new_attr_id = 794;
                                            break;
                                        }
                                    break;
                                    case 206:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "внутренняя/наружная  резьба":
                                                $new_attr_id = 1445;
                                            break;
                                        }
                                    break;
                                    case 130:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "190":
                                            case "30":
                                                $new_attr_id = null;
                                            break;
                                        }
                                    break;
                                    case 131:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "190":
                                            case "30":
                                                $new_attr_id = null;
                                            break;
                                        }
                                    break;
                                    case 116:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "115 В":
                                                $new_attr_id = 802;
                                            break;
                                            case "220-230 В":
                                            case "230":
                                            case "230 В":
                                                $new_attr_id = 801;
                                            break;
                                            case "24 В":
                                            case "25":
                                                $new_attr_id = 805;
                                            break;
                                        }
                                    break;
                                    case 127:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "Helix V":
                                                $new_attr_id = 827;
                                            break;
                                            case "Stratos-Z":
                                                $new_attr_id = 836;
                                            break;
                                            case "Stratos GIGA-D":
                                                $new_attr_id = 904;
                                            break;
                                            case "Stratos PICO":
                                                $new_attr_id = 890;
                                            break;
                                            case "TMVarios PICO-STG":
                                                $new_attr_id = 963;
                                            break;
                                        }
                                    break;
                                    case 27:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "да":
                                                $new_attr_id = 142;
                                            break;
                                            case "нет":
                                                $new_attr_id = 143;
                                            break;
                                        }
                                    break;
                                    case 132:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "0,8 bar":
                                                $new_attr_id = 1085;
                                            break;
                                            case "1,5 bar":
                                                $new_attr_id = 1087;
                                            break;
                                            case "0,1 bar":
                                                $new_attr_id = 2152;
                                            break;
                                            case "0,5 bar":
                                                $new_attr_id = 2153;
                                            break;
                                            case "0,6 bar":
                                                $new_attr_id = 2154;
                                            break;
                                            case "0,7 bar":
                                                $new_attr_id = 2155;
                                            break;
                                            case "0,8 bar":
                                                $new_attr_id = 2156;
                                            break;
                                            case "0,9 bar":
                                                $new_attr_id = 2157;
                                            break;
                                            case "1,1 bar":
                                                $new_attr_id = 2158;
                                            break;
                                            case "1,2 bar":
                                                $new_attr_id = 2159;
                                            break;
                                            case "1,3 bar":
                                                $new_attr_id = 2160;
                                            break;
                                            case "1,4 bar":
                                                $new_attr_id = 2161;
                                            break;
                                            case "1,5 bar":
                                                $new_attr_id = 2162;
                                            break;
                                            case "1,6 bar":
                                                $new_attr_id = 2163;
                                            break;
                                            case "1,7 bar":
                                                $new_attr_id = 2164;
                                            break;
                                            case "1,8 bar":
                                                $new_attr_id = 2165;
                                            break;
                                            case "1,9 bar":
                                                $new_attr_id = 2166;
                                            break;
                                            case "10":
                                                $new_attr_id = 1091;
                                            break;
                                            case "12":
                                                $new_attr_id = 2167;
                                            break;
                                            case "25":
                                                $new_attr_id = 1095;
                                            break;
                                            case "6":
                                                $new_attr_id = 1089;
                                            break;
                                            case "6,5 bar":
                                                $new_attr_id = 2168;
                                            break;
                                            case "3,1 bar":
                                                $new_attr_id = 2188;
                                            break;
                                            case "3,2 bar":
                                                $new_attr_id = 2189;
                                            break;
                                            case "3,3 bar":
                                                $new_attr_id = 2190;
                                            break;
                                            case "3,4 bar":
                                                $new_attr_id = 2191;
                                            break;
                                            case "3,5 bar":
                                                $new_attr_id = 2192;
                                            break;
                                            case "3,6 bar":
                                                $new_attr_id = 2193;
                                            break;
                                            case "3,7 bar":
                                                $new_attr_id = 2194;
                                            break;
                                            case "3,8 bar":
                                                $new_attr_id = 2195;
                                            break;
                                            case "4,1 bar":
                                                $new_attr_id = 2205;
                                            break;
                                            case "4,3 bar":
                                                $new_attr_id = 2206;
                                            break;
                                            case "4,5 bar":
                                                $new_attr_id = 2207;
                                            break;
                                            case "4,8 bar":
                                                $new_attr_id = 2208;
                                            break;
                                            case "5,3 bar":
                                                $new_attr_id = 2217;
                                            break;
                                            case "5,4 bar":
                                                $new_attr_id = 2218;
                                            break;
                                            case "5,8 bar":
                                                $new_attr_id = 2219;
                                            break;
                                            case "5,9 bar":
                                                $new_attr_id = 2220;
                                            break;
                                            case "5,5 bar":
                                                $new_attr_id = 2221;
                                            break;
                                            case "15,2 bar":
                                                $new_attr_id = 2233;
                                            break;
                                            case "2,1 bar":
                                                $new_attr_id = 2236;
                                            break;
                                            case "2,2 bar":
                                                $new_attr_id = 2237;
                                            break;
                                            case "2,3 bar":
                                                $new_attr_id = 2238;
                                            break;
                                            case "2,4 bar":
                                                $new_attr_id = 2239;
                                            break;
                                            case "2,5 bar":
                                                $new_attr_id = 2240;
                                            break;
                                            case "2,6 bar":
                                                $new_attr_id = 2241;
                                            break;
                                            case "2,7 bar":
                                                $new_attr_id = 2242;
                                            break;
                                            case "2,8 bar":
                                                $new_attr_id = 2243;
                                            break;
                                            case "2,9 bar":
                                                $new_attr_id = 2244;
                                            break;
                                        }
                                    break;
                                    case 133:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "1~ 230 V / 50 H":
                                                $new_attr_id = 1100;
                                            break;
                                            case "3~ 400 V \/ 50 Hz":
                                                $new_attr_id = 1102;
                                            break;
                                        }
                                    break;
                                    case 88:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "Отвод":
                                            case "Тройник":
                                                $new_attr_id = null;
                                            break;
                                            case "Хомут":
                                                $new_attr_id = 413;
                                            break;
                                        }
                                    break;
                                    case 89:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "от -50 до 250":
                                                $new_attr_id = 2538;
                                            break;
                                        }
                                    break;
                                    case 134:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "1.4301":
                                            case "Нержавеющая Сталь":
                                            case "Нержавеющая Сталь":
                                            case "Нержавеящая сталь":
                                                $new_attr_id = 1106;
                                            break;
                                            case "EN-GJL (серый чугун)":
                                                $new_attr_id = 1108;
                                            break;
                                            case "Cталь":
                                                $new_attr_id = 2250;
                                            break;
                                            case "PK-GF30":
                                                $new_attr_id = null;
                                            break;
                                        }
                                    break;
                                    case 135:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "PPE/PS-GF30":
                                                $new_attr_id = null;
                                            break;
                                            case "Синтетический материал (полипропилен)":
                                                $new_attr_id = 1110;
                                            break;
                                            case "Нержавеющая Сталь":
                                            case "Нержавеящая сталь":
                                                $new_attr_id = 1112;
                                            break;
                                        }
                                    break;
                                    case 136:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "нет":
                                                $new_attr_id = 1115;
                                            break;
                                        }
                                    break;
                                    case 142:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "внутренняя":
                                                $new_attr_id = 1124;
                                            break;
                                            case "наружная":
                                                $new_attr_id = 1125;
                                            break;
                                        }
                                    break;
                                    case 154:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "0 - 1":
                                            case "0-1,0":
                                                $new_attr_id = 1146;
                                            break;
                                            case "0-0,25":
                                                $new_attr_id = 1143;
                                            break;
                                            case "0-0,4":
                                                $new_attr_id = 1144;
                                            break;
                                            case "0-0,6":
                                                $new_attr_id = 1145;
                                            break;
                                            case "0-1,6":
                                                $new_attr_id = 1147;
                                            break;
                                            case "0-2,5":
                                                $new_attr_id = 1148;
                                            break;
                                        }
                                    break;
                                    case 156:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "М20х1,5":
                                                $new_attr_id = 1167;
                                            break;
                                            case "30":
                                            case "внутренняя/наружная резьба":
                                                $new_attr_id = null;
                                            break;
                                            case "G1/1":
                                                $new_attr_id = 1164;
                                            break;
                                        }
                                    break;
                                    case 140:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "G":
                                                $new_attr_id = 1121;
                                            break;
                                            case "N":
                                                $new_attr_id = 1122;
                                            break;
                                            case "NG":
                                                $new_attr_id = 1123;
                                            break;
                                            case "S":
                                                $new_attr_id = 1120;
                                            break;
                                            case "WRV":
                                                $new_attr_id = 1118;
                                            break;
                                            case "WDV Premium":
                                                $new_attr_id = 1119;
                                            break;
                                            case "ГРАНЛЕВЕЛ тип А":
                                                $new_attr_id = 2607;
                                            break;
                                            case "WAO":
                                                $new_attr_id = 2603;
                                            break;
                                            case "WAV":
                                                $new_attr_id = 2604;
                                            break;
                                        }
                                    break;
                                    case 157:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "+110°С":
                                                $new_attr_id = 1197;
                                            break;
                                            case "+120°С":
                                                $new_attr_id = 1198;
                                            break;
                                            case "+140°С":
                                                $new_attr_id = 1200;
                                            break;
                                            case "+150°С":
                                                $new_attr_id = 1201;
                                            break;
                                            case "+200°С":
                                                $new_attr_id = 1204;
                                            break;
                                            case "+60°С":
                                                $new_attr_id = 1192;
                                            break;
                                            case "+90°С":
                                                $new_attr_id = 1195;
                                            break;
                                            case "80ºС":
                                                $new_attr_id = 1194;
                                            break;
                                            case "95ºС":
                                                $new_attr_id = 2255;
                                            break;
                                        }
                                    break;
                                    case 163:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "1":
                                                $new_attr_id = 2278;
                                            break;
                                            case "1,6":
                                                $new_attr_id = 1237;
                                            break;
                                            case "25":
                                                $new_attr_id = 2279;
                                            break;
                                        }
                                    break;
                                    case 181:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "0.2":
                                                $new_attr_id = 1335;
                                            break;
                                            case "0.25":
                                                $new_attr_id = 1330;
                                            break;
                                            case "0.5":
                                                $new_attr_id = 1331;
                                            break;
                                            case "1.0":
                                                $new_attr_id = 1332;
                                            break;
                                        }
                                    break;
                                    case 190:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "1":
                                                $new_attr_id = 1368;
                                            break;
                                            case "1 1/4":
                                                $new_attr_id = 1372;
                                            break;
                                            case "G1,1/2":
                                                $new_attr_id = 1369;
                                            break;
                                            case "G2 1/4":
                                                $new_attr_id = 2317;
                                            break;
                                        }
                                    break;
                                    case 191:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "G 1":
                                                $new_attr_id = 1375;
                                            break;
                                            case "G 1 1/4":
                                                $new_attr_id = 1379;
                                            break;
                                            case "G 2":
                                                $new_attr_id = 1378;
                                            break;
                                            case "G 3/4":
                                                $new_attr_id = 1374;
                                            break;
                                            case "G1B":
                                            case "G2B":
                                            case "G3/4B":
                                            case "G5/4B":
                                                $new_attr_id = null;
                                            break;
                                        }
                                    break;
                                    case 193:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "трехходовой":
                                                $new_attr_id = 1381;
                                            break;
                                        }
                                    break;
                                    case 170:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "Батарея (3.6 В)":
                                                $new_attr_id = null;
                                            break;
                                        }
                                    break;
                                    case 194:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "ручное (рычаг)":
                                                $new_attr_id = null;
                                            break;
                                        }
                                    break;
                                    case 195:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "полнопроходной":
                                            case "цельносварной":
                                                $new_attr_id = null;
                                            break;
                                        }
                                    break;
                                    case 197:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "Нержавеющая сталь":
                                                $new_attr_id = 1403;
                                            break;
                                        }
                                    break;
                                    case 198:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "AISI 409":
                                                $new_attr_id = 1408;
                                            break;
                                            case "cталь 20X13":
                                            case "Сталь 20X13":
                                                $new_attr_id = 1406;
                                            break;
                                        }
                                    break;
                                    case 238:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "Уплотнение":
                                                $new_attr_id = 1537;
                                            break;
                                        }
                                    break;
                                    case 240:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "0,75":
                                                $new_attr_id = 1538;
                                            break;
                                            case "1,5":
                                                $new_attr_id = 1540;
                                            break;
                                            case "2,2":
                                                $new_attr_id = 1541;
                                            break;
                                            case "4,0":
                                                $new_attr_id = 1543;
                                            break;
                                            case "0,37":
                                                $new_attr_id = 2333;
                                            break;
                                            case "110":
                                                $new_attr_id = 2337;
                                            break;
                                            case "165":
                                                $new_attr_id = 2338;
                                            break;
                                            case "30":
                                                $new_attr_id = 2339;
                                            break;
                                            case "37":
                                                $new_attr_id = 2340;
                                            break;
                                            case "630":
                                                $new_attr_id = 2342;
                                            break;
                                            case "75":
                                                $new_attr_id = 2343;
                                            break;
                                            case "90":
                                                $new_attr_id = 2344;
                                            break;
                                        }
                                    break;
                                    case 244:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "Внутренняя резьба":
                                                $new_attr_id = 2345;
                                            break;
                                        }
                                    break;
                                    case 255:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "наружная резьба":
                                                $new_attr_id = 1593;
                                            break;
                                            case "Внутренняя резьба":
                                            case "внутренняя резьба":
                                                $new_attr_id = 1591;
                                            break;
                                        }
                                    break;
                                    case 269:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "нет":
                                                $new_attr_id = 1609;
                                            break;
                                            case "да":
                                                $new_attr_id = 1608;
                                            break;
                                        }
                                    break;
                                    case 270:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "Nitril":
                                                $new_attr_id = 2346;
                                            break;
                                        }
                                    break;
                                    case 346:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "CAS 178":
                                            case "CAS 180":
                                            case "CAS 181":
                                                $new_attr_id = 2609;
                                            break;
                                            case "KP 61":
                                            case "KP 62":
                                            case "KP 63":
                                            case "KP 75":
                                            case "KP 77":
                                            case "KP 81":
                                            case "КР 73":
                                            case "КР 98":
                                                $new_attr_id = 2038;
                                            break;
                                            case "KPS 76":
                                            case "KPS 77":
                                            case "KPS 79":
                                            case "KPS 80":
                                            case "KPS 81":
                                            case "KPS 83":
                                                $new_attr_id = 2039;
                                            break;
                                            case "MBC 8100":
                                                $new_attr_id = 2610;
                                            break;
                                            case "RT 101":
                                            case "RT 101L":
                                            case "RT 102":
                                            case "RT 106":
                                            case "RT 107":
                                            case "RT 11":
                                            case "RT 115":
                                            case "RT 12":
                                            case "RT 120":
                                            case "RT 123":
                                            case "RT 124":
                                            case "RT 13":
                                            case "RT 14":
                                            case "RT 141":
                                            case "RT 14L":
                                            case "RT 15":
                                            case "RT 17":
                                            case "RT 2":
                                            case "RT 23":
                                            case "RT 24":
                                            case "RT 26":
                                            case "RT 270":
                                            case "RT 271":
                                            case "RT 3":
                                            case "RT 4":
                                            case "RT 7":
                                            case "RT 8":
                                            case "RT 8L":
                                            case "RT 9":
                                            case "RT3":
                                                $new_attr_id = 2037;
                                            break;
                                        }
                                    break;
                                    case 273:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "Приварной":
                                                $new_attr_id = 1612;
                                            break;
                                        }
                                    break;
                                    case 275:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "обрезиненный":
                                                $new_attr_id = 1616;
                                            break;
                                        }
                                    break;
                                    case 276:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "Статический ":
                                                $new_attr_id = 2347;
                                            break;
                                        }
                                    break;
                                    case 281:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "AISI304":
                                                $new_attr_id = 1638;
                                            break;
                                            case "AISI316":
                                                $new_attr_id = 1639;
                                            break;
                                            case "Nitril":
                                                $new_attr_id = 1636;
                                            break;
                                            case "Viton":
                                                $new_attr_id = 1637;
                                            break;
                                        }
                                    break;
                                    case 287:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "Присоединительные фитинги":
                                                $new_attr_id = 1653;
                                            break;
                                        }
                                    break;
                                    case 306:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "#N/A":
                                                $new_attr_id = null;
                                            break;
                                            case "2,2":
                                                $new_attr_id = 2394;
                                            break;
                                            case "4,2":
                                                $new_attr_id = 2404;
                                            break;
                                            case "6,8":
                                                $new_attr_id = 2411;
                                            break;
                                            case "9,6":
                                                $new_attr_id = 2422;
                                            break;
                                        }
                                    break;
                                    case 307:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "#N/A":
                                                $new_attr_id = null;
                                            break;
                                        }
                                    break;
                                    case 308:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "#N/A":
                                                $new_attr_id = null;
                                            break;
                                        }
                                    break;
                                    case 178:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "44318":
                                            case "44348":
                                                $new_attr_id = null;
                                            break;
                                        }
                                    break;
                                    case 316:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "Переменный (AC)":
                                                $new_attr_id = 1777;
                                            break;
                                            case "Переменный/Постоянный (AC/DC)":
                                                $new_attr_id = 1779;
                                            break;
                                        }
                                    break;
                                    case 335:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "S08A":
                                                $new_attr_id = 1906;
                                            break;
                                        }
                                    break;
                                    case 336:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "PD ES04D0K-4T-IP65":
                                                $new_attr_id = 1937;
                                            break;
                                        }
                                    break;
                                    case 349:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "Комплект для монтажа":
                                                $new_attr_id = 2043;
                                            break;
                                        }
                                    break;
                                    case 35:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "А":
                                                $new_attr_id = 160;
                                            break;
                                            case "АА":
                                                $new_attr_id = 159;
                                            break;
                                            case "В":
                                                $new_attr_id = 161;
                                            break;
                                            case "С":
                                                $new_attr_id = 162;
                                            break;
                                        }
                                    break;
                                    case 4:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "Межфланцевое":
                                                $new_attr_id = null;
                                            break;
                                        }
                                    break;
                                    case 40:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "Сталь":
                                            case "Чугун":
                                                $new_attr_id = null;
                                            break;
                                            case "16-Oct":
                                                $new_attr_id = 192;
                                            break;
                                            case "6,3":
                                                $new_attr_id = 191;
                                            break;
                                        }
                                    break;
                                    case 44:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "200":
                                                $new_attr_id = null;
                                            break;
                                            case "Никелированная латунь":
                                                $new_attr_id = 239;
                                            break;
                                            case "ч":
                                            case "чугун":
                                                $new_attr_id = 233;
                                            break;
                                        }
                                    break;
                                    case 50:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "Шаровый":
                                                $new_attr_id = 264;
                                            break;
                                        }
                                    break;
                                    case 6:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "50":
                                                $new_attr_id = 2522;
                                            break;
                                        }
                                    break;
                                    case 60:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "G 1 1/2":
                                                $new_attr_id = 296;
                                            break;
                                            case "G 1 1/4":
                                                $new_attr_id = 295;
                                            break;
                                            case "G 1/2":
                                                $new_attr_id = 291;
                                            break;
                                            case "G 1/2A":
                                                $new_attr_id = 300;
                                            break;
                                            case "G 1/4":
                                                $new_attr_id = 290;
                                            break;
                                            case "G 1/4A":
                                                $new_attr_id = 299;
                                            break;
                                            case "G 3/8":
                                                $new_attr_id = 293;
                                            break;
                                            case "G 3/8-18 NPT":
                                                $new_attr_id = 304;
                                            break;
                                            case "G1/2 А":
                                                $new_attr_id = 300;
                                            break;
                                            case "М10x1":
                                            case "М10х1":
                                                $new_attr_id = 306;
                                            break;
                                            case "М14x1.5":
                                            case "M14 x 1.5":
                                            case "М14х1,5":
                                                $new_attr_id = 307;
                                            break;
                                            case "M 18x1.5":
                                            case "M18 x 1.5":
                                                $new_attr_id = 311;
                                            break;
                                            case "М20x1.5":
                                            case "M20 x 1.5":
                                            case "М20x1,5":
                                                $new_attr_id = 309;
                                            break;
                                            case "M 27x2":
                                                $new_attr_id = 312;
                                            break;
                                            case "R 1/4":
                                                $new_attr_id = 2526;
                                            break;
                                            case "М10х1":
                                                $new_attr_id = 2526;
                                            break;
                                            case "М6х1":
                                            case "М6х1":
                                                $new_attr_id = 305;
                                            break;
                                        }
                                    break;
                                    case 84:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "Внешняя":
                                                $new_attr_id = 2530;
                                            break;
                                            case "Внутренняя":
                                                $new_attr_id = 2529;
                                            break;
                                            case "НР/ВР":
                                                $new_attr_id = 392;
                                            break;
                                        }
                                    break;
                                    case 84:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "от -50 до 250":
                                                $new_attr_id = 2538;
                                            break;
                                        }
                                    break;
                                    case 92:
                                        switch($attr_value){
                                            default:
                                                $this->error_write($fp, $item, $attr_id, $attr_value);
                                            break;
                                            case "-0,2-10":
                                                $new_attr_id = 498;
                                            break;
                                            case "-0,2-8":
                                                $new_attr_id = 475;
                                            break;
                                            case "0,5-2":
                                                $new_attr_id = 505;
                                            break;
                                            case "-0,5-2":
                                                $new_attr_id = 479;
                                            break;
                                            case "-0,5-3":
                                                $new_attr_id = 480;
                                            break;
                                            case "0,5-3,0":
                                                $new_attr_id = 508;
                                            break;
                                            case "0,5-4":
                                                $new_attr_id = 509;
                                            break;
                                            case "-0,7-6":
                                                $new_attr_id = 511;
                                            break;
                                            case "0-6":
                                                $new_attr_id = 477;
                                            break;
                                            case "1-10":
                                                $new_attr_id = 478;
                                            break;
                                            case "1-6":
                                                $new_attr_id = 512;
                                            break;
                                            case "2-12":
                                                $new_attr_id = 521;
                                            break;
                                            case "4-12":
                                                $new_attr_id = 476;
                                            break;
                                            case "5-16":
                                                $new_attr_id = 484;
                                            break;
                                            case "5-20":
                                                $new_attr_id = 485;
                                            break;
                                            case "5-24":
                                                $new_attr_id = 531;
                                            break;
                                            case "5-30":
                                                $new_attr_id = 486;
                                            break;
                                            case "7-20":
                                                $new_attr_id = 487;
                                            break;
                                            case "От 0 до 0,3":
                                                $new_attr_id = 488;
                                            break;
                                            case "От 0 до 2,5":
                                                $new_attr_id = 491;
                                            break;
                                            case "От 0 до 3,5":
                                                $new_attr_id = 492;
                                            break;
                                            case "От 0,1 до 1,0":
                                                $new_attr_id = 494;
                                            break;
                                            case "От 0,1 до 1,1":
                                                $new_attr_id = 495;
                                            break;
                                            case "От 0,1 до 1,5":
                                                $new_attr_id = 496;
                                            break;
                                            case "От -0,2 до 1,0":
                                                $new_attr_id = 497;
                                            break;
                                            case "От 0,2 до 2,5":
                                                $new_attr_id = 499;
                                            break;
                                            case "От 0,2 до 3":
                                                $new_attr_id = 500;
                                            break;
                                            case "От -0,2 до 4":
                                                $new_attr_id = 501;
                                            break;
                                            case "От 0,2 до 6":
                                                $new_attr_id = 502;
                                            break;
                                            case "От -0,2 до 8":
                                                $new_attr_id = 475;
                                            break;
                                            case "От 0,3 до 5":
                                                $new_attr_id = 504;
                                            break;
                                            case "От 0,5 до 3":
                                                $new_attr_id = 508;
                                            break;
                                            case "От 0,5 до 4":
                                                $new_attr_id = 509;
                                            break;
                                            case "От 0,5 до 6":
                                                $new_attr_id = 510;
                                            break;
                                            case "От 1,5 до 11":
                                                $new_attr_id = 514;
                                            break;
                                            case "От -0,2 до 7,5":
                                                $new_attr_id = 503;
                                            break;
                                        }
                                    break;
                                }
                            }

                            if(isset($new_attr_id) || !empty($cav)){
                                \Catalog\Model\CatalogAttributeRelation::create([
                                    "attribute_id" => $attr_id,
                                    "cid" => $item->id,
                                    "attribute_value_id" => empty($cav) ? $new_attr_id : $cav->id,
                                ]);
                            }

                        } else {
                            \Catalog\Model\CatalogAttributeRelation::create([
                                "value_text" => $attr_value,
                                "attribute_id" => $attr_id,
                                "cid" => $item->id,
                            ]);
                        }
                    }
                }
            }
        }
        fclose($fp);
    }
}
