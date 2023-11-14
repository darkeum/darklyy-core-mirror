<?php

namespace Catalog_v2\Model;

class Categories extends \Model\Model{
    protected $fillable = ["name", "altname", "fulltitle", "description", "full", "keywords", "module", "icon", "preview", "position", "position_filter", "parent_id", "sum", "hidden", "status"];
}