<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class WorkerProfile extends Eloquent
{

    const WORKER_PENDING_STATUS='pending';
    const WORKER_ACCEPT_STATUS='accept';
    const WORKER_REJECT_STATUS='reject';
    const WORKER_AVAILABLE_STATUS='available';
    const WORKER_UNAVAILABLE_STATUS='unavailable';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
