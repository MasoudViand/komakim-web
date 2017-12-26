<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class Service extends Eloquent
{


    public function subcategory()
    {
        return $this->belongsTo('App\Subcategory');
    }

    public function serviceQuestions()
    {

        return $this->hasMany('App\ServiceQuestion');
    }
}
