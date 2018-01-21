<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class Transaction extends Eloquent
{
    protected $dates = ['created_at'];
    const CHARGE_FROM_BANK = 'chargeFromBank';
    const PAY_ORDER        = 'payOrder';
    const BALANCE_ACCOUNT  = 'balanceAccount';
    const DONE_ORDER       = 'doneOrder';
}
