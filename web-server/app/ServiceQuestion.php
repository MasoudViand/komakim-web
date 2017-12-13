<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class ServiceQuestion extends Eloquent
{
    public function service()
    {
        return $this->belongsTo('App\ServiceQuestion');
    }
}
