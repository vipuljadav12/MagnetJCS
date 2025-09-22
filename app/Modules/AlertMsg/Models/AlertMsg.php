<?php

namespace App\Modules\AlertMsg\Models;

use Illuminate\Database\Eloquent\Model;

class AlertMsg extends Model
{
  protected $table = 'common_alert_msg';
  protected $primaryKey = 'id';

  protected $fillable = [
    'msg_title',
    'msg_txt'
  ];
}
