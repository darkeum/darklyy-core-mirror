<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (21.07.2023 6:01)
*/

namespace Controls\Models;


use Catalog\Models\Catalog;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Model\Model;

class ControlStorage extends Model
{
    protected $fillable = [ 'title',  'date',  'status'];

    public function control_storage_detail(): HasOne
    {
        return $this->hasOne(ControlStorageDetail::class);
    }

    public function catalog(): HasOne
    {
        return $this->hasOne(Catalog::class, 'id');
    }

    public static function getStatus(): array
    {
        return [
            1 => 'Ожидает проверки',
            2 => "Отправлен на доработку",
            3 => 'Согласован',
        ];
    }

}