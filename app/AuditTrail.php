<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    protected $fillable = [
    	"user_id",
    	"previous_url",
    	"path",
    	"method",
    	"response",
    	"attributes",
    	"status_code",
    ];
}
