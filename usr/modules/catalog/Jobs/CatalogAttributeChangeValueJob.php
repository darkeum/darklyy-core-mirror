<?php
 
namespace Catalog\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Boot\System\Bus\Dispatchable;
use Illuminate\Bus\Batchable;
 
class CatalogAttributeChangeValueJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $catalog_attribute_value;

    public function __construct(\Catalog\Model\CatalogAttributeValue $catalog_attribute_value){
        $this->catalog_attribute_value = $catalog_attribute_value;
    }
 
    public function handle(){
        $is_trashed = $this->catalog_attribute_value->trashed();
        $this->catalog_attribute_value->catalog_attribute_relation()->lazyById()->each(function($item) use ($is_trashed){
            if($is_trashed){
                $item->delete();
            }
            \Catalog\Jobs\CatalogAttributeJob::dispatch($item->catalog);
        });

        if($is_trashed){
            $this->catalog_attribute_value->forceDelete();
        }
    }
}