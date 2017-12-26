<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class WorkerProfile extends Eloquent
{

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
