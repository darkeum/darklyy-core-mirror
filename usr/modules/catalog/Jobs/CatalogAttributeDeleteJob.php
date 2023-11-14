<?php
 
namespace Catalog\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Boot\System\Bus\Dispatchable;
use Illuminate\Bus\Batchable;
 
class CatalogAttributeDeleteJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $catalog_attribute;

    public function __construct(\Catalog\Model\CatalogAttribute $catalog_attribute){
        $this->catalog_attribute = $catalog_attribute;
    }
 
    public function handle(){
        //получить товары этого атрибута
        $class = \Catalog\Attribute\Attribute::getClassByType($this->catalog_attribute->type);
        $this->catalog_attribute->attribute_relation()->lazyById()->each(function($item){
            //удалить catalog_attribute_relation
            $item->delete();
            //перекешировать товар
            \Catalog\Jobs\CatalogAttributeJob::dispatch($item->catalog);
        });
        //удаление по типу
        $class::delete($this->catalog_attribute);
        //удалить связь с категориями
        $this->catalog_attribute->attribute_category()->delete();
        //удалить атрибут
        $this->catalog_attribute->forceDelete();
    }
}