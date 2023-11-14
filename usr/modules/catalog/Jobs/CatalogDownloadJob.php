<?php

namespace Catalog\Jobs;

use Boot\Support\Numb;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Boot\System\Bus\Dispatchable;
use Catalog\Models\CertRelation;
use Illuminate\Bus\Batchable;

//Загрузка на портал
class CatalogDownloadJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    protected $file;
    protected $finished_file;
    protected $uid;
    protected $array_name;
    protected $num;

    public function __construct($file, $finished_file, $uid, $array_name, $num)
    {
        $this->file = $file;
        $this->finished_file = $finished_file;
        $this->uid = $uid;
        $this->array_name = $array_name;
        $this->num = $num;
    }

    public function handle()
    {
        if ($this->batch()->cancelled()) {
            // Determine if the batch has been cancelled...
            return;
        }

        //dd($this->finished_file);
        //dd($this->finished_file);

        $excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->file);
        // $countRow =  $excel->setActiveSheetIndex(0)->getHighestRow(); //количество строк
        $countColumnAlp =  $excel->setActiveSheetIndex(0)->getHighestColumn(); //количество столбцов в буквенном виде
        $countColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($countColumnAlp); //количество столбцов в числовом виде с 

        $catalog_attribute_items = [];

        //проход по столбцам
        $item = new \stdClass;
        $item->catalog = null;
        $item->color = null;
        $item->text = null;
        $item->error = false;

        try {
            $spec = ["1" => null, "2" => null, "3" => null];
            $specFlag = false;
            $weight = ["1" => null, "2" => null, "3" => null, "4" => null, "5" => null];
            $weightFlag = false;
            $attribute = [];
            $id = null;
            $sku = null;
            $cat = null;
            $cert_flag = false;
            $cert_ids = [];
            $data = [];

            for ($j = 1; $j <= $countColumn; $j++) {
                $flag_no_empty = true;
                $val = $excel->getActiveSheet()->getCellByColumnAndRow($j, $this->num)->getFormattedValue();
                if (empty($this->array_name[$j])) {
                    continue;
                }
                if (empty($val) && $val !== 0 && $val !== "0") $flag_no_empty = false;
                $val = $flag_no_empty ? trim($val) : null;
                //получение id
                if ($this->array_name[$j] === 'id') {
                    if ($flag_no_empty) {
                        $id = $val;

                        $item->text = "ОБНОВИТЬ";
                        $item->color = "green";
                        $item->catalog = \Catalog_v2\Model\Catalog::findOrFail($id);
                    }
                    //добавление в spec
                } else if (strstr($this->array_name[$j], "spec_")) {
                    if ($flag_no_empty) {
                        $num = explode("_", $this->array_name[$j]);
                        $spec[$num[1]] = $val;
                        $specFlag = true;
                    }
                    //добавление в weight
                } else if (strstr($this->array_name[$j], "weight_")) {
                    if ($flag_no_empty) {
                        $num = explode("_", $this->array_name[$j]);
                        $weight[$num[1]] = $val;
                        $weightFlag = true;
                    }
                    //добавление в certificate
                } else if (strstr($this->array_name[$j], "certificate_")) {
                    if ($flag_no_empty) {
                        $val = Numb::dotsReplace($val);
                        foreach (explode(',', $val) as $cert_id) {
                            if(!empty(\Catalog\Models\Cert::find(intval(trim($cert_id))))){
                                $cert_ids[$cert_id] = intval(trim($cert_id));
                            }                           
                        }
                    } 
                    $cert_flag = true;                   
                    //добавление в attribute
                } else if (strstr($this->array_name[$j], "attribute_")) {
                    $num = explode("_", $this->array_name[$j]);
                    $attribute_id = $num[1];
                    $attribute_value = $val;

                    if (array_key_exists($attribute_id, $catalog_attribute_items)) {
                        $catalog_attribute = $catalog_attribute_items[$attribute_id];
                    } else {
                        $catalog_attribute = \Catalog\Model\CatalogAttribute::find($attribute_id);
                        $catalog_attribute_items[$attribute_id] = $catalog_attribute;
                    }

                    if (empty($catalog_attribute)) {
                        throw new \Exception("Атрибут id: {$attribute_id} не найден!");
                    }

                    $attribute_class = \Catalog\Attribute\Attribute::getClassByType($catalog_attribute->type);
                    if (!empty($attribute_value)) {
                        $attribute_class::checkAttributeValue($catalog_attribute, $attribute_value);
                    }

                    $attribute[$attribute_id] = (object)[
                        "attribute" => $catalog_attribute,
                        "attribute_class" => $attribute_class,
                        "value" => $attribute_value,
                    ];
                    //категории
                } else if (strstr($this->array_name[$j], "category")) {
                    $data[$this->array_name[$j]] = $val;
                    if ($flag_no_empty) $cat = $val;
                    //производитель
                } else if (strstr($this->array_name[$j], "man")) {
                    $data[$this->array_name[$j]] = $val;
                    //наименование
                } else if (strstr($this->array_name[$j], "title")) {
                    $data[$this->array_name[$j]] = $val;
                    //артикул
                } else if (strstr($this->array_name[$j], "sku")) {
                    $data[$this->array_name[$j]] = $val;
                    if ($flag_no_empty && $this->array_name[$j] === "sku") $sku = $val;
                    //наименование группы товаров
                } else if (strstr($this->array_name[$j], "catalog_groups_id")) {
                    if ($flag_no_empty) {
                        $cg = \Catalog_v2\Model\CatalogGroups::where("name", $val)->first();
                        if (!empty($cg)) {
                            $cg_id = $cg->id;
                            $data[$this->array_name[$j]] = $cg_id;
                        }
                    } else {
                        $data[$this->array_name[$j]] = null;
                    }
                    //добавление в общий
                } else {
                    $data[$this->array_name[$j]] = $val;
                }
            }

            // if(count($attribute) > 0) $data["attribute"] = $attribute;
            if ($specFlag) $data["spec"] = $spec;
            if ($weightFlag) $data["weight"] = $weight;

         

            if (empty($id) && empty($cat) && empty($sku)) {
                throw new \Exception("Нет категории и артикула! Создание не возможно.");
            }

            if (empty($id)) {
                $ca = \Catalog_v2\Model\Catalog::where("title", "like", $data["title"]);
                if (!empty($sku)) $ca->orWhere("sku", "like", $sku);
                if ($ca->exists()) {
                    $item->catalog = $ca->first();
                    throw new \Exception("Такой товар уже есть!");
                } else {
                    $item->text = "СОЗДАТЬ";
                    $item->color = "green";
                }
            } else {
                if (empty($cat)) {
                    $cat = str_replace(",", "", $item->catalog->category);
                    if (empty($cat) && count($attribute) > 0) {
                        throw new \Exception("Категория не указана, загрузка атрибутов не доступна");
                    }
                }
            }

            //проверка атрибутов на категорию
            foreach ($attribute as $attribute_id => $attr) {
                if (!empty($attr->value)) {
                    if (\Catalog\Model\CatalogAttributeCategory::where("attribute_id",  $attribute_id)->where("category_id", $cat)->doesntExist()) {
                        throw new \Exception("Атрибут id: { $attribute_id} не принадлежит категории {$cat}");
                    }
                }
            }

            $data["uid"] = $this->uid;
            if (empty($id)) {
                $item->catalog = \Catalog_v2\Model\Catalog::create($data);
            } else {
                $item->catalog->update($data);
            }

            //обновление сертификатов
            if($cert_flag){
                CertRelation::where('cid', $item->catalog->id)->delete();
                foreach ($cert_ids as $value_cert) {
                    CertRelation::create([
                        'cid' => $item->catalog->id,
                        'cert_id' => $value_cert,
                    ]);
                }
                
            }

            //обновление атрибутов
            if (count($attribute) > 0) {
                foreach ($attribute as $attribute_id => $attr) {
                    if (empty($attr->value)) {
                        $attr->attribute_class::deleteRelation($item->catalog, $attr->attribute);
                    } else {
                        $attr->attribute_class::updateOrCreate($item->catalog, $attr->attribute, $attr->value);
                    }
                }
                \Catalog\Jobs\CatalogAttributeJob::dispatchSync($item->catalog);
                $item->catalog = $item->catalog->fresh();
            }
        } catch (\Exception $e) {
            $item->color = "red";
            $item->error = $e->getMessage();
        }

        $document = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->finished_file);
        $sheet = $document->setActiveSheetIndex(0);
        $countRow =  $sheet->getHighestRow(); //количество строк
        $countRow++;

        $element = [];
        if ($item->error) {
            $element[] = $item->error;
        } else {
            $element[] = $item->text;
        }
        if (!is_null($item->catalog)) {
            foreach ($this->array_name as $name) {
                if (empty($name)) {
                    $element[] = "";
                    continue;
                }
                if (strstr($name, "spec_")) {
                    $num = explode("_", $name);
                    $element[] = $item->catalog->spec[$num[1]] ?? "";
                    //добавление в weight
                } else if (strstr($name, "weight_")) {
                    $num = explode("_", $name);
                    $element[] = $item->catalog->weight[$num[1]] ?? "";
                    //добавление в attribute
                } else if (strstr($name, "attribute_")) {
                    $num = explode("_", $name);
                    $element[] = $item->catalog->attribute[$num[1]] ?? "";
                } else {
                    $element[] = $item->catalog->{$name} ?? "";
                }
            }
        }
        $startLine = 1;
        foreach ($element as $el) {
            $sheet->setCellValueByColumnAndRow($startLine, $countRow, $el);
            $startLine++;
        }
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($document, 'Xlsx');
        $objWriter->save($this->finished_file);
    }
}
