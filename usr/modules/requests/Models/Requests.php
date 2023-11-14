<?php

namespace Requests\Models;

use Boot\Models\Attach;
use Boot\Models\Model;

class Requests extends Model
{
    protected $table = "request";

    protected $fillable = [
        'uid_dir', 'uid_exec', 'title',
        'title_user', 'request', 'request_data',
        'result', 'oid', 'object',
        'org', 'type', 'aid',
        'final_cid', 'pid', 'specification',
        'date', 'status', 'url_result',
    ];

    protected $casts = [
        'request_data' => "array",
        'result' => "array",
    ];

    public function attach()
    {
        return $this->hasMany(Attach::class, "pid", 'id')
            ->where('module', '=', 'request');
    }

    public function attachRequestCatalog()
    {
        return $this->hasMany(Attach::class, "pid", 'id')
            ->where('module', '=', 'request_catalog');
    }
}
