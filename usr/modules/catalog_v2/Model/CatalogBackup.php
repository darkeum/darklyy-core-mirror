<?php

namespace Catalog_v2\Model;

class CatalogBackup extends \Model\Model{
    protected $connection = 'atrienergo_backup';
    protected $fillable = ["uid", "cid", "original", "changes", "comment"];

    protected $casts = [
        'original' => "array",
        'changes' => "array",
    ];
}