<?php

class PriceTypeSelect{
    protected $data;

    public $id;
    public $cid;
    public $type = null;

    public function __construct(){
        $this->data = \Price\Models\CatalogPriceType::get_cache_all();
        $this->prepare_data = $this->data->whereNull("parent");
    }

    public function get_data(){
        return $this->data;
    }
    
    public function get_prepare_data(){
        return $this->prepare_data;
    }

    public function init_option(){
        $option = [];
        $prepare_type = $this->prepare_type($this->type);
        foreach($this->prepare_data as $item){
            $selected = $item->id == $prepare_type ? "selected" : "";
            $option[] = "<option value='{$item->id}' {$selected}>{$item->title}</option>";
        }
        return implode("", $option);
    }

    public function get_options(){
        $option = [];
        $prepare_type = $this->prepare_type($this->type);
        foreach($this->prepare_data as $item){
            $option[] = "<option value='{$item->id}'>{$item->title}</option>";
        }
        return [
            "option" => $option,
            "price_type" => $prepare_type,
        ];
    }

    public function prepare_type($type){
        if(is_null($type)) return $type;

        $parent = $this->data->find($type);
        if(empty($parent->parent)) return $type;

        return $this->prepare_type($parent->parent);
    }

    public function view(){
        if(empty($this->cid)) throw new \Exception("Field `cid` is empty");

        $option = $this->init_option();
        return "<select 
            class=\"form-control priceTypeSelect\" 
            onchange=\"\$priceTypeSelect.init_price(this, {id: '{$this->id}', cid: '{$this->cid}'})\"
            data-id=\"{$this->id}\"
            data-cid=\"{$this->cid}\"
        >
            {$option}
        </select>";
    }

    static function getPrice($request){
        $typeData = \Price\Models\CatalogPriceType::find($request["type"] ?? 4);
        switch($typeData->type){
            default:
            case "OTHER":
                return null;
            break;
            case "PRICE":
                $price = \Price::getPrice($request["cid"]);
                return [
                    "id" => $request["cid"],
                    "catalog_price_detail_id" => $price->price_list_detail->id ?? null,
                    "price" => $price->price_list_discount_,
                    "price_type" => $price->price_list_type->id ?? null,
                    "original_price" => $price->price_list_,
                    "clean_price" => $price->price_list_detail->sum ?? 0,
                    "discount" => $price->price_discount->discount ?? 0,
                ];
            break;
            case "PURCHASE":
                $price = \Price::getPrice($request["cid"]);
                return [
                    "id" => $request["cid"],
                    "catalog_price_detail_id" => $price->price_purchase_detail->id ?? null,
                    "price" => $price->price_purchase_,
                    "price_type" => $price->price_purchase_type->id ?? null,
                    "original_price" => $price->price_purchase_,
                    "clean_price" => $price->price_purchase_,
                    "discount" => 0,
                ];
            break;
        }
    }

    static function script(){
        return <<<HTML
            <script>
                const \$priceTypeSelect = {
                    link: "/ajax.php?do=getPriceTypeSelectData",

                    async init_price(select, request){
                        request.type = select.value;
                        const data = request;
                        data.data = await this.get_price(request);
                        this.set_price(data);
                    },
                
                    async get_price(request){    
                        const data = new FormData;
                        for(let key in request) data.append(key, request[key]);
                        return await (await fetch(this.link, { method: 'POST', body: data })).json();
                    },
                
                    set_price(data){
                        alert("Function `set_price` not init!");
                    },
                }
            </script>
HTML;
    }
}