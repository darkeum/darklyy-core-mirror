<?php

namespace Catalog_v2;

//main
//http://www.cnppump.ltd/DetailInfo/PumpDetail.aspx?SeriesID=8&PumpID=995

//точки
//http://www.cnppump.ltd/Service/PumpCurveHandler.ashx?cmd=GetChartInitialInfo&SeriesID=8&PumpID=995&Languge=cn&SoftType=web&from=ebook&IsAddLog=1&_=1649920756429

//img
//http://www.cnppump.ltd/Data/Series8/b2e0f8e1525f47409f2baf55c16820a7.png

//data
//http://www.cnppump.ltd/Service/PartBaseHandler.ashx?cmd=GetPropListByPumpID2&SeriesID=8&PumpID=995&Languge=cn&_=1649922887127

class Pump{

    static function points($command){
        static::items(function($item){
            static::point($item);
        }, $command);
    }

    static function images($command){
        static::items(function($item){
            static::image($item);
        }, $command);
    }

    static function point($item){
        //берем точки
        $id = static::get_query($item->link);
        $point_url = "http://www.cnppump.ltd/Service/PumpCurveHandler.ashx?cmd=GetChartInitialInfo&{$id}&Languge=cn&SoftType=web&from=ebook&IsAddLog=1&_=1649920756429";
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $point_url);
        $points_data = json_decode($res->getBody());
        // dump($points_data);
        foreach($points_data->ChartObject as $name => $obj){
            if($name != "SpectrumShape" && !empty($obj)){
                $data = [];
                foreach($obj as $o){
                    $data[] = (object)["Q" => str_replace(".", ",", $o->X), "H" => str_replace(".", ",", $o->Y)];
                }
                $element = [
                    "cid" => $item->cid,
                    "uid" => 79,
                    "uid_change" => 79,
                    "data" => $data,
                    "type_of_instalation" => 0,
                    "work_line" => 1,
                    "type" => str_replace("MaxWaterCurve", "", $name),
                ];
                // dump($element);
                \Catalog_v2\Model\Pump::create($element);
            }
        }
    }

    static function image($item){
        //создаем сертификат
        $catalog = \Catalog_v2\Model\Catalog::findOrFail($item->cid);
        $cert = \Catalog_v2\Model\Cert::create([
            "uid" => 79, 
            "type" => 13, 
            "date_add" => date("Y-m-d"), 
            "comment" => $catalog->sku, 
            "status" => 1
        ]);
        // $cert = \Catalog_v2\Model\Cert::findOrFail(4369);

        //сохраняем картинку
        $img_name = static::find_img(static::main_data($item->link));
        parse_str(static::get_query($item->link), $output);
        $img_url = "http://www.cnppump.ltd/Data/Series{$output['SeriesID']}/{$img_name}";
        $img_attach = "files/cert/{$cert->id}/{$img_name}";
        @mkdir(dirname(ROOT.$img_attach), 0777, true);
        copy($img_url, ROOT.$img_attach);

        //сохраняем в attach
        $attach = \Boot\Models\Attach::create([
            "name" => $img_name, 
            "url" => $img_attach, 
            "pid" => $cert->id, 
            "module" => "cert", 
            "access" => 1,
        ]);

        $cert->update(["aid" => $attach->id]);

        $cert_rel = \Catalog_v2\Model\CertRelation::create([
            "cid" => $catalog->id,
            "cert_id" => $cert->id,
        ]);

        // dump($cert);
    }

    static function get_query($link){
        $url = parse_url($link);
        return $url['query'];
    }

    static function items($callback, $command){
        $query = \Catalog_v2\Model\PumpLinks::where("status", 1);
        $bar = $command->getOutput()->createProgressBar($query->count());
        $bar->setFormat(
            "%message%\n%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%"
        );
        $bar->setMessage('Start');
        $bar->start();
        $query->chunkById(100, function($items) use ($callback, &$bar, &$command){
            foreach($items as $item){
                try {
                    $bar->setMessage("Record {$item->id} is being prepared");
                    $callback($item);
                    $item->update(["status" => 2]);
                    $bar->advance();
                } catch (\Exception $e) {
                    $command->error($e->getMessage());
                }
                sleep(2);
            }
        });
        $bar->finish();
    }

    static function main_data($url){
        $id = static::get_query($url);
        $data_url = "http://www.cnppump.ltd/Service/PartBaseHandler.ashx?cmd=GetPropListByPumpID2&{$id}&Languge=cn&_=1649922887127";
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $data_url);
        return json_decode($res->getBody());
    }

    static function find_img($main_data){
        foreach($main_data->PartPropList[0] as $elem){
            if($elem->PropName == "ProductFileName"){
                return $elem->PropValue;
            }
        }
    }

    static function data(){
        return [
            ['id'  => '161412', 'link' => 'http://www.cnppump.ltd/DetailInfo/PumpDetail.aspx?SeriesID=10&PumpID=3630'],
        ];
    }
}