<?php
 
namespace Catalog\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Boot\System\Bus\Dispatchable;
use Illuminate\Bus\Batchable;
use \Catalog\Attribute\Attribute;
 
class CatalogAttributeChangeTypeJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $attribute;
    protected $old_type;
    protected $new_type;

    public function __construct(\Catalog\Model\CatalogAttribute $attribute, $old_type, $new_type){
        $this->attribute = $attribute;
        $this->old_type = $old_type;
        $this->new_type = $new_type;
    }
 
    public function handle(){
        //блокируем catalog_attribute
        $this->attribute->update(["blocked" => 1]);
        //приводим к стандартным данным
        $class_old = Attribute::getClassByType($this->old_type);
        $data = $class_old::getStandardData($this->attribute);
        //передаем стандартные данные в новый тип
        $class_new = Attribute::getClassByType($this->new_type);
        $class_new::setStandardData($this->attribute, $data);
        //действия в конце
        $class_old::clearStandardData($data);
        //разблокируем catalog_attribute
        $this->attribute->update(["blocked" => 0]);
    }
}