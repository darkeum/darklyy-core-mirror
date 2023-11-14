<?php

namespace Catalog_v2\Model;

class Cert extends \Model\Model{
    protected $fillable = ["aid", "uid", "type", "date_add", "date_edit", "date_start", "date_end", "issue", "numb", "comment", "status"];
}