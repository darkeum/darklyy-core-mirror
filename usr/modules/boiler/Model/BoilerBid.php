<?php

namespace Boiler\Model;

class BoilerBid extends \Boot\Models\Model{
    protected $fillable = ["archive", "numb_counter", "numb", "title", "comment", "client_title", "client_comment", "date_create", "date_end", "uid", "object", "org", "oid", "nds_includes", "sales_link", "progress", "status"];

    
}