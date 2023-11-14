<?php

namespace Realization\Model;

class RealizationTorg extends \Model\Model{
    
    protected $fillable = ["realization_id", "base_doc", "base_numb", "base_date", "oid_bank", "consignee", "consignee_bank", "oid_address", "org_bank", "shipper", "shipper_bank", "leader", "buch", "ship", "ship_post", "procuratory_id"];
}