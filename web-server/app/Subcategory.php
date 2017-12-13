<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class Subcategory extends Eloquent
{



    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function services()
    {

        return $this->hasMany('App\Service');
    }
}
