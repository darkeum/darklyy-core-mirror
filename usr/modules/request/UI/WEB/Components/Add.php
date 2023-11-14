<?php

namespace Request\UI\WEB\Components;

use Boot\System\Livewire\Component;
use Boot\Support\Facades\DB;
use Boot\Support\Facades\Auth;
use Requests\Models\Request as ModelRequest;

class Add extends Component
{
    public $boiler_id = null;
    public $id_data;
    public $select_obj;
    public $electric_data;
    public $station_data;
    public $url_result;
    public $data;
    public $switch = false;

    public function mount($electric_data, $station_data, $request_id)
    {
        $this->electric_data = $electric_data;
        $this->station_data = $station_data;
        $this->id_data = $request_id;


        $this->data = ModelRequest::find($request_id);
        $this->url_result = json_decode($this->data->url_result);

        $this->select_obj = static::getSelect([
            "name" => "boiler_id",
            "ajaxUrl" => "getBoiler",
            "select_class" => "object_select",
            "select_theme" => 'default',
            'required' => "required"
        ]);
    }

    public function render()
    {
        return view('request::manager.components.add_bid');
    }

    public function switch()
    {
        if ($this->switch == false)
            $this->switch = true;
        else
            $this->switch = false;
    }

    public function submit()
    {

        if ($this->switch == false) {
            $bid = $this->boiler_id;
        } else {
            require_once(ROOT . "usr" . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "boiler" . DIRECTORY_SEPARATOR . "admin" . DIRECTORY_SEPARATOR . "sub" . DIRECTORY_SEPARATOR . "bid.php");
            $bid = bid_add();
        } 

        $boilerBidTypeArr = array(

            "bid_id" => $bid,
            "section" => 5,
            "type" => 23,
            "allow_category" => "[407,382]",

        );


        $bid_type_id = DB::table('boiler_bid_type')->insertGetId($boilerBidTypeArr);

        $boilerBidDataArr = array(

            "bid_type_id" => $bid_type_id,
            "category" => 407,
            "uid" => Auth::id()

        );

        $bid_data_id_407 = DB::table('boiler_bid_data')->insertGetId($boilerBidDataArr);
        $boilerBidDataArr['category'] = 382;

        $bid_data_id_382 = DB::table('boiler_bid_data')->insertGetId($boilerBidDataArr);


        $array_NS = array();
        foreach ($this->station_data as $item) {
            $array_NS[] = array(
                'cid' => $item['tm_cid'],
                'count' => $item['tm_count'],
            );
        }
        $request = array(
            "id" => $bid_data_id_407,
            'goods' => $array_NS
        );
        require_once(ROOT . "usr" . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "boiler" . DIRECTORY_SEPARATOR . "admin" . DIRECTORY_SEPARATOR . "sub" . DIRECTORY_SEPARATOR . "boiler_function.php");
        bid_data_detail_add_model($request);

        $array_EL = array();
        foreach ($this->electric_data as $item) {
            $array_EL[] = array(
                'cid' => $item['tm_cid'],
                'count' => $item['tm_count'],
            );
        }

        $request['id'] = $bid_data_id_382;
        $request['goods'] = $array_EL;

        bid_data_detail_add_model($request);

        $urlArray = array();
        $this->url_result = ModelRequest::find($this->id_data)->url_result;

        if (!empty($this->url_result)) {
            foreach (json_decode($this->url_result) as $url) {
                $urlArray[] = $url;
            }
        }

        $urlArray[] = "/module/boiler/bid/detail/" . $bid;

        $urlArray = json_encode($urlArray);

        $this->url_result = json_decode($urlArray);

        $this->data->url_result = $urlArray;

        $this->data->save();
    }

    private static function getSelect($request)
    {
        $oSelectSettingJS = [
            "mod" => "jsReturn",
            "name" => $request["name"],
            "ajaxUrl" => $request["ajaxUrl"],
            "id" => $request["id"] ?? null,
            "return" => true,
            "bootstrap" => true,
            "custom" => $request["custom"] ?? null,
            "required" => $request["required"] ?? null,
        ];
        if (isset($request["select_class"])) {
            $oSelectSettingJS["select_class"] = $request["select_class"];
        }
        if (isset($request["select_theme"])) {
            $oSelectSettingJS["select_theme"] = $request["select_theme"];
        }
        $oSelectSettingSelect = $oSelectSettingJS;
        $oSelectSettingSelect["mod"] = "select";
        return [
            "js" => oSelectCustom($oSelectSettingJS),
            "select" => oSelectCustom($oSelectSettingSelect),
        ];
    }
}
