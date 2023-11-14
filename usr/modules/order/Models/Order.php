<?php

namespace Order\Models;

use \Boot\Models\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;
use \Boot\Support\Facades\Auth;

class Order extends Model {

	use SoftDeletes;

	protected $fillable = [
	    "uid",
        "surname",
        "name",
        "ochestvo",
        "fio_source",
        "phone",
        "phone_source",
        "email",
        "type",
        "message",
        "org_name",
        "org_inn",
        "org_id",
        "object_name",
        "object_address",
        "object_id",
        "request_source",
        "visit_date",
        "read_status",
        "read_uid",
        "status"
    ];

	protected $casts = [
        // 'published_at' => "datetime",
        // 'tags' => "array",
        'read_uid' => "array",
        'fio_source' => "array",
        'phone_source' => "array",
    ];
}