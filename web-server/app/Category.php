<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Category extends Eloquent
{
    public function subcategories()
    {

        return $this->hasMany('App\Subcategory');
    }
}
