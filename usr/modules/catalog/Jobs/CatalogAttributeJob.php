<?php
 
namespace Catalog\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Boot\System\Bus\Dispatchable;
use Illuminate\Bus\Batchable;
 
class CatalogAttributeJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $catalog;

    public function __construct(\Catalog_v2\Model\Catalog $catalog){
        $this->catalog = $catalog;
    }
 
    public function handle(){
        $attribute = [];
        $attribute_save = [];
        $catalog_attribute_relation = \Catalog\Model\CatalogAttributeRelation::with(["attribute_value", "attribute"])->where("cid", $this->catalog->id)->get();
        foreach($catalog_attribute_relation as $item){
            $value = $value_save = null;

            $class = \Catalog\Attribute\Attribute::getClassByType($item->attribute->type);
            $data = $class::getValues($item);
            
            $value = $data->value;
            $value_save = $data->value_save;

            if(!empty($value)){
                $attribute[ $item->attribute_id ] = $value;
            }
            if(!empty($value_save)){
                $attribute_save[ $item->attribute_id ] = $value_save;
            }
        }
        $this->catalog->attribute = $attribute;
        $this->catalog->attribute_save = $attribute_save;
        $this->catalog->save();
    }
}