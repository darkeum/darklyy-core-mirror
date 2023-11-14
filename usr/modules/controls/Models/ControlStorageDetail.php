<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (21.07.2023 6:22)
*/

namespace Controls\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Model\Model;
use Storage\Models\StorageDetail;

class ControlStorageDetail extends Model
{
    protected $fillable = ["control_storage_id", "storage_id", "storage_detail_id", "status"];

    public function storage_detail(): HasOne
    {
        return $this->hasOne(StorageDetail::class, 'id', 'storage_detail_id');
    }

    public function control_storage(): BelongsTo
    {
        return $this->belongsTo(ControlStorage::class);
    }
}