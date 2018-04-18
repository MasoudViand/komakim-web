<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class DiscountCode extends Eloquent
{

    const PERCENT_TYPE      ='percent';
    const CONST_AMOUNT_TYPE ='const_amount';

}
