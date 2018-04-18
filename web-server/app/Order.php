<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class Order extends Eloquent
{

    const ACCEPTED_SERVICE_STATUS='accepted';
    const PENDING_SERVICE_STATUS='pending';
    const REJECTED_SERVICE_STATUS='rejected';
    protected $collection ='orders';

//    protected $hidden = [
//        'revisions',
//    ];
}
