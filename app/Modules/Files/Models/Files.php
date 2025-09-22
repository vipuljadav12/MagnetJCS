<?php

namespace App\Modules\Files\Models;

use Illuminate\Database\Eloquent\Model;

class Files extends Model {

    //
    protected $table='landing_links';
    protected $primaryKey='link_id';
    protected $fillable=[
      'district_id',
      'link_title',
      'popup_text',
      'link_filename',
      'sort_order',
      'link_url'
    ];

}
