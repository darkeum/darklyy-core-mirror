<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Catalog\Models;

use \bb;
use Boot\Models\Categories;
use Boot\Support\Str;
use Boot\Models\Model;
use Boot\Support\Numb;
use Boot\Support\Facades\Auth;
use Boot\System\Scout\Searchable;
use Catalog\Models\CatalogAttribute;
use Catalog_v2\Model\CatalogTypes;
use Catalog_v2\Model\CatalogBackup;
use Illuminate\Database\Eloquent\Builder;
use Darkeum\JsonAttributes\Casts\JsonAttributes;
use Price;

class Catalog extends Model
{
    use Searchable;
    static $NEW_SKU = "НОВЫЙ_АРТИКУЛ";

    protected $fillable = ["sync_id", "sku", "sku2", "sku3", "sku_ozon", "sku_UdmurtSnab", "unit", "title", "fulltitle", "short", "full", "uid", "tags", "price_data", "category", "attach_id", "altname", "preview", "keywords", "description", "man", "allow_rating", "allow_index", "score", "votes", "views", "clicks", "attribute", "attribute_save", "publish_urls", "fixed", "types", "spec", "weight", "date_delivery", "qmin", "qnorm", "hidden", "check_update", "active", "active_guest", "active_user", "catalog_groups_id", "created_at", "updated_at", "use_integration"];

    protected $attributes = [
        'spec' => "[]",
        'publish_urls' => "[]",
        'weight' => "[]",
        'attribute' => "[]",
        'attribute_save' => "[]",
        'attach_id' => "[]",
        'hidden' => 0,
    ];

    protected $casts = [
        'spec' => "array",
        'publish_urls' => "array",
        'weight' => "array",
        'attribute' => "array",
        'attribute_save' => JsonAttributes::class,
        'attach_id' => "array",
        'price_data' => "array",
    ];

    protected $catalog_backup_comment = "Обновление данных";

    public function scopeWithAttributeSave(): Builder
    {
        return $this->attribute_save->modelScope();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uid = empty($model->uid) ? Auth::id() : $model->uid;
        });

        static::created(function ($model) {
            $bb = new bb;
            $model->short =  $bb->parse(processText(filter(fileInit('catalog', $model->id, 'content', $model->short, 'html')), $model->id, true));
            $model->full =  $bb->parse(processText(filter(fileInit('catalog', $model->id, 'content', $model->full, 'html')), $model->id, true));
            $model->sku = static::generate_sku($model);

            $model->saveQuietly();

            // $model->setLog($model, ["comment" => "Добавление номенклатуры"]);

            updateQuantity($model->id, 1, 0, "set", ["comment" => 'Добавление товара']);
            // syncRegister('catalog_v2', $model->id, '1', '0');
            $sync = new \Sync\Sync;
            $sync->syncRegister('catalog_v2', $model->id, '1', '0');
        });

        static::updating(function ($model) {
            $model->sku = static::generate_sku($model);
            // if(!empty($model->weight[2]) && !empty($model->weight[6]) && !empty($model->weight[7])){
            //     $model->weight[3] = Numb::prepareNumeric($model->weight[2]) * Numb::prepareNumeric($model->weight[6]) * Numb::prepareNumeric($model->weight[7]);
            // }
            // if(!empty($model->weight[8]) && !empty($model->weight[9]) && !empty($model->weight[10])){
            //     $model->weight[11] = Numb::prepareNumeric($model->weight[8]) * Numb::prepareNumeric($model->weight[9]) * Numb::prepareNumeric($model->weight[10]);
            // }
            // $model->setLog($model, ["comment" => "Обновление номенклатуры Data_before: ".$model->toJson()]);
        });

        static::updated(function ($model) {
            if ($model->wasChanged("sku") || $model->wasChanged("title") || $model->wasChanged("unit") || $model->wasChanged("types")) {
                // syncRegister('catalog_v2', $model->id, '2', '0');
                $sync = new \Sync\Sync;
                $sync->syncRegister('catalog_v2', $model->id, '2', '0');
            }
            // CatalogBackup::create(["uid" => Auth::id() ?? $model->uid, "cid" => $model->id, "changes" => $model->changes, "original" => $model->original, "comment" => $model->catalog_backup_comment]);
        });

        static::saving(function ($model) {
            $model->title = Str::squish($model->title);
            $model->category = empty($model->category) ? null : "," . str_replace(",", "", $model->category) . ",";

            $preview = parse_url($model->preview ?? "");
            $model->preview = $preview["path"] ?? "";
            $weight = $model->weight;
            if (!empty($model->weight[2]) && !empty($model->weight[6]) && !empty($model->weight[7])) {
                $weight[3] = Numb::prepareNumeric($model->weight[2]) * Numb::prepareNumeric($model->weight[6]) * Numb::prepareNumeric($model->weight[7]);
            }
            if (!empty($model->weight[8]) && !empty($model->weight[9]) && !empty($model->weight[10])) {
                $weight[11] = Numb::prepareNumeric($model->weight[8]) * Numb::prepareNumeric($model->weight[9]) * Numb::prepareNumeric($model->weight[10]);
            }
            $model->weight = $weight;
        });

        static::saved(function ($model) {
            $model->altname = empty($model->altname) ? translit($model->title) . $model->id : mb_strtolower(str_replace(array('-', ' '), array('_', '_'), $model->altname), 'UTF-8');

            static::update_catalog_groups_id($model);

            $model->saveQuietly();
        });
    }

    public function catalog_manufacture()
    {
        return $this->belongsTo(\Catalog_v2\Model\CatalogManufacture::class, "man");
    }
    public function catalog_unit()
    {
        return $this->belongsTo(\Catalog_v2\Model\CatalogUnit::class, "unit", "unit");
    }
    public function catalog_price_temp()
    {
        return $this->hasOne(\Price\Models\CatalogPriceTemp::class, "id");
    }
    public function stock_detail()
    {
        return $this->hasOne(\Stocks\Models\StockDetail::class, "cid");
    }
    public function user()
    {
        return $this->belongsTo(\Boot\Models\Users::class, "uid");
    }
    public function catalog_price_rival()
    {
        return $this->hasOne(\Price\Models\CatalogPriceRival::class, "id");
    }
    public function pump()
    {
        return $this->hasMany(\Catalog_v2\Model\Pump::class, "cid");
    }
    public function catalog_groups()
    {
        return $this->belongsTo(\Catalog_v2\Model\CatalogGroups::class, "catalog_groups_id");
    }

    public function catalog_cert()
    {
        return $this->hasMany(\Catalog\Models\CertRelation::class, "cid", 'id');
    }


    public static function generate_sku($model)
    {
        $sku = Str::squish($model->sku);
        $category = str_replace(",", "", $model->category);
        // if($model->sku == "AE05".$category."XXXXXX"){
        if ($sku == static::$NEW_SKU) {
            $n1 = str_pad($category, 3, "0", STR_PAD_LEFT);
            $n2 = str_pad($model->id, 6, "0", STR_PAD_LEFT);
            $sku = "AE05" . $n1 . $n2;
        }
        return $sku;
    }

    public static function update_catalog_groups_id($model)
    {
        if ($model->wasChanged("catalog_groups_id")) {
            if (empty($model->catalog_groups_id)) {
                \Price\Models\CatalogPriceDiscountOrganizations::where("cid", "=", $model->id)->delete();
            } else {
                $data = \Price\Models\CatalogPriceDiscountGroups::where("catalog_groups_id", $model->catalog_groups_id)->get();
                foreach ($data as $r) {
                    $data2 = \Price\Models\CatalogPriceDiscountOrganizations::where("oid", $r->oid)->where("cid", $model->id)->get();
                    if (empty($r->type)) {
                        $data2->delete();
                    } else {
                        \Price\Models\CatalogPriceDiscountOrganizations::updateOrCreate(
                            ["oid" => $r->oid, "cid" => $model->id],
                            ["type" => $r->type, "auto" => 1]
                        );
                    }
                }
            }
        }
    }

    static public function getCatalog($parameter = array())
    {
        $query = static::where("hidden", $parameter['hidden'] ?? '0');
        if (isset($parameter["active"])) $query->where("active", $parameter['active']);
        if (isset($parameter["id"])) $query->where("id", $parameter['id']);
        if (isset($parameter["man"]) && !empty($parameter["man"])) {
            if (is_array($parameter["man"])) {
                $query->whereIn("man", $parameter['man']);
            } else {
                $query->where("man", $parameter['man']);
            }
        }
        if (isset($parameter["sku"])) $query->where("sku", $parameter['sku']);
        if (isset($parameter["sku_like"])) $query->where("sku", "LIKE", "%{$parameter['sku_like']}%");
        if (isset($parameter["category"])) $query->where("category", "LIKE", "%,{$parameter['category']},%");

        if (isset($parameter['attribute'])) {
            foreach ($parameter['attribute'] as $key => $value) {
                if (is_array($value)) {
                    $query->where(function ($q) use ($value, $key) {
                        foreach ($value as $v) {
                            $q->orWhereRaw("JSON_VALUE (`attribute` , '$." . $key . "') IN ('" . $v . "')");
                        }
                    });
                } else {
                    $query->whereRaw("JSON_VALUE (`attribute` , '$." . $key . "') IN ('" . $value . "')");
                }
            }
        }

        $sql = \Boot\Support\Str::replaceArray('?', $query->getBindings(), $query->toSql());
        $model = $query->get();
        $data = $model->toArray();
        if (empty($data)) {
            // dump('Не нашел по параметрам: '. $sql);
            $attr = [];
            $attr['uid'] = [79, 1];
            $attr['title'] = "Не найдена номенклатура";
            $attr['text'] = "'Не нашел по параметрам: '. {$sql}";
            $attr['icon'] = config('app.url') . '/files/push/new_storage.png';
            $attr['url'] = "";
            $attr['pNotif'] = true;
            $attr['pPush'] = true;
            $attr['module'] = "calculation";
            $attr['pid'] = 0;
            // sendAllNotif($attr);
            return null;
        } else {
            return $data;
        }
    }

    public function change_catalog_backup_comment($new_comment)
    {
        $this->catalog_backup_comment = $new_comment;
    }

    static function get_translate_field_list()
    {
        return [
            "id" => "id",
            "sku" => "артикул",
            "sku2" => "артикул2",
            "sku3" => "артикул3",
            "sku_ozon" => "артикул Озон",
            "sku_UdmurtSnab" => "артикул УдмуртСнаб",
            "unit" => "единицы измерения",
            "title" => "наименование",
            "fulltitle" => "расширенный заголовок",
            "short" => "описание",
            "full" => "full",
            "uid" => "пользователь",
            "tags" => "теги",
            "price_data" => "price_data",
            "category" => "категория",
            "attach_id" => "attach_id",
            "altname" => "ссылка",
            "preview" => "изображение",
            "keywords" => "ключевые слова",
            "description" => "description",
            "man" => "производитель",
            "allow_rating" => "allow_rating",
            "allow_index" => "allow_index",
            "score" => "score",
            "votes" => "votes",
            "views" => "просмотры",
            "clicks" => "clicks",
            "attribute" => "атрибуты",
            "publish_urls" => "publish_urls",
            "fixed" => "fixed",
            "types" => "тип",
            "spec" => "spec",
            "weight" => "weight",
            "date_delivery" => "дата поставки (в днях)",
            "qmin" => "qmin",
            "qnorm" => "qnorm",
            "hidden" => "скрытие",
            "check_update" => "check_update",
            "active" => "активность",
            "active_guest" => "активность для гостей",
            "active_user" => "активность для пользователей",
            "catalog_groups_id" => "группа товаров",
            "created_at" => "дата создания",
            "updated_at" => "дата обновления",
        ];
    }

    static function translate_field($field)
    {
        $translate = static::get_translate_field_list();
        return $translate[$field] ?? false;
    }

    public function catalog_types()
    {
        return $this->belongsTo(CatalogTypes::class, "types");
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {


        $category_id = str_replace(",", "", $this->category);

        // $dynamic_attributes = [];
        // $numeric_attributes = [];
        // $non_numeric_attributes = [];
        // $attribute = CatalogAttribute::with(["attribute_category_one" => function ($query) use ($category_id) {
        //     $query->where("category_id", $category_id);
        // }])->whereHas("attribute_category", function ($query) use ($category_id) {
        //     $query->where("category_id", $category_id)->orderBy("position")->orderBy("id");
        // })->get();
        // foreach ($attribute as $key => $value) {
        //     $dynamic_attributes[] =  $value->id . '-' . $value->name;
        //     if (isset($this->attribute[$value->id])) {
        //         $non_numeric_attributes[$value->id . '-' . $value->name] = $this->attribute[$value->id];
        //     }
        // }
        // dump($dynamic_attributes);
        // dump($non_numeric_attributes);
        // dd( $this->attribute);

        $category = [];
        $category_lv = [];
        $category_lv[0] = ['Catalog'];
        $cat = Categories::find($category_id);
        if (!empty($cat)) {
            $category[] = $cat->name;
            $category_lv[1] = ['Catalog > ' . $cat->name];
            if (!empty($cat->parent_id)) {
                $cat2 = Categories::find($cat->parent_id);
                $category[] = $cat2->name;
                $category_lv[1] = ['Catalog > ' . $cat2->name];
                $category_lv[2] = ['Catalog > ' . $cat2->name . ' > ' . $cat->name];
                if (!empty($cat2->parent_id)) {
                    $cat3 = Categories::find($cat2->parent_id);
                    $category[] = $cat3->name;
                    $category_lv[1] = ['Catalog > ' . $cat3->name];
                    $category_lv[2] = ['Catalog > ' . $cat3->name . ' > ' . $cat2->name];
                    $category_lv[3] = ['Catalog > ' . $cat3->name . ' > ' . $cat2->name . ' > ' . $cat->name];
                    if (!empty($cat3->parent_id)) {
                        $cat4 = Categories::find($cat3->parent_id);
                        $category[] = $cat4->name;
                        $category_lv[1] = ['Catalog > ' . $cat4->name];
                        $category_lv[2] = ['Catalog > ' . $cat4->name . ' > ' . $cat3->name];
                        $category_lv[3] = ['Catalog > ' . $cat4->name . ' > ' . $cat3->name . ' > ' . $cat2->name];
                        $category_lv[4] = ['Catalog > ' . $cat4->name . ' > ' . $cat3->name . ' > ' . $cat2->name . ' > ' . $cat->name];
                        if (!empty($cat4->parent_id)) {
                            $cat5 = Categories::find($cat4->parent_id);
                            $category[] = $cat5->name;
                            $category_lv[1] = ['Catalog > ' . $cat5->name];
                            $category_lv[2] = ['Catalog > ' . $cat5->name . ' > ' . $cat4->name];
                            $category_lv[3] = ['Catalog > ' . $cat5->name . ' > ' . $cat4->name . ' > ' . $cat3->name];
                            $category_lv[4] = ['Catalog > ' . $cat5->name . ' > ' . $cat4->name . ' > ' . $cat3->name . ' > ' . $cat2->name];
                            $category_lv[5] = ['Catalog > ' . $cat5->name . ' > ' . $cat4->name . ' > ' . $cat3->name . ' > ' . $cat2->name . ' > ' . $cat->name];
                        }
                    }
                }
            }
        }
        $category[] = 'Catalog';
        // foreach ($category_lv as $key => $value) {
        //     $category_lv_last =  [$value[0] . ' > Catalog'];
        // }
        // $category_lv[] = $category_lv_last ?? 'Catalog';
        // dd($category);
        $result = array_merge(
            $this->toArray(),
            [
                // Cast id to string and turn created_at into an int32 timestamp
                // in order to maintain compatibility with the Typesense index definition below
                'id' => (string) $this->id,
                'name' => (string) $this->title,
                'description' => (string) $this->short,
                'brand' => (string) isset($this->catalog_manufacture) ? $this->catalog_manufacture->name : '',
                'categories' => array_reverse($category),
                'categories.lvl0' => $category_lv[0] ?? [''],
                'categories.lvl1' => $category_lv[1] ?? [''],
                'categories.lvl2' => $category_lv[2] ?? [''],
                'categories.lvl3' => $category_lv[3] ?? [''],
                'categories.lvl4' => $category_lv[4] ?? [''],
                'categories.lvl5' => $category_lv[5] ?? [''],
                'categories.lvl6' => $category_lv[6] ?? [''],
                'image' => (string) 'https://atri-energo.ru/' . $this->preview,
                'price' =>  \Price::getPrice($this->id)->price_list_,
                'popularity' => $this->clicks ?? 0,
                'free_shipping' => true,
                'rating' => 3,
                // 'dynamic_attributes' => $dynamic_attributes,
                // 'non_numeric_attributes' => $non_numeric_attributes,
                // 'numeric_attributes' => [],
            ]
        );
        // dump($result);
        return $result;
    }

    /**
     * The Typesense schema to be created.
     *
     * @return array
     */
    public function getCollectionSchema(): array
    {
        // $atr = CatalogAttribute::all();

        $result =  [
            'name' => $this->searchableAs(),
            "enable_nested_fields" => true,
            'fields' => [
                [
                    'name' => 'id',
                    'type' => 'string',
                    'facet' => false,
                ],
                [
                    'name' => 'name',
                    'type' => 'string',
                    'facet' => false,
                ],
                [
                    'name' => 'description',
                    'type' => 'string',
                    'facet' => false,
                ],
                [
                    'name' => 'brand',
                    'type' => 'string',
                    'facet' => true,
                ],
                [
                    'name' => 'categories',
                    'type' => 'string[]',
                    'facet' => true,
                ],
                [
                    'name' => 'categories.lvl0',
                    'type' => 'string[]',
                    'facet' => true,
                ],
                [
                    'name' => 'categories.lvl1',
                    'type' => 'string[]',
                    'facet' => true,
                ],
                [
                    'name' => 'categories.lvl2',
                    'type' => 'string[]',
                    'facet' => true,
                ],
                [
                    'name' => 'categories.lvl3',
                    'type' => 'string[]',
                    'facet' => true,
                ],
                [
                    'name' => 'categories.lvl4',
                    'type' => 'string[]',
                    'facet' => true,
                ],
                [
                    'name' => 'categories.lvl5',
                    'type' => 'string[]',
                    'facet' => true,
                ],
                [
                    'name' => 'categories.lvl6',
                    'type' => 'string[]',
                    'facet' => true,
                ],
                [
                    'name' => 'price',
                    'type' => 'float',
                    'facet' => true,
                ],
                [
                    'name' => 'popularity',
                    'type' => 'int32',
                    'facet' => false,
                ],
                [
                    'name' => 'free_shipping',
                    'type' => 'bool',
                    'facet' => true,
                ],
                [
                    'name' => 'rating',
                    'type' => 'int32',
                    'facet' => true,
                ],

                // [
                //     'name' => 'numeric_attributes',
                //     'type' => 'string[]',
                //     'facet' => true,
                // ],
                // [
                //     'name' => 'non_numeric_attributes',
                //     'type' => 'object',
                //     'facet' => true,
                // ],
                // [
                //     'name' => 'dynamic_attributes',
                //     'type' => 'string[]',
                //     'facet' => true,
                // ],



            ],
            'token_separators' => ['-'],
            'default_sorting_field' => 'popularity',
        ];
        return $result;
    }

    /**
     * The fields to be queried against. See https://typesense.org/docs/0.21.0/api/documents.html#search.
     *
     * @return array
     */
    public function typesenseQueryBy(): array
    {
        return [
            'title', 'sku'
        ];
    }
}
