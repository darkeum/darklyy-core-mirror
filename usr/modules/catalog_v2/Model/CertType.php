<?php

namespace Catalog_v2\Model;

class CertType extends \Model\Model{
    protected $fillable = ["title", "print", "status", "date_start", "date_end"];

    protected $casts = [
        'date_start' => "boolean",
        'date_end' => "boolean",
    ];
}