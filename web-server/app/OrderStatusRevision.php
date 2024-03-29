<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class OrderStatusRevision extends Eloquent
{
    const WAITING_FOR_WORKER_STATUS         ='waitingForStatus';
    const ACCEPT_ORDER_BY_WORKER_STATUS     ='acceptByWorker';
    const START_ORDER_BY_WORKER_STATUS      ='startByWorker';
    const EDIT_BY_WORKER_STATUS             ='editByWorker';
    const APPROVE_EDIT_BY_CLIENT_STATUS     ='approveByClient';
    const DENIED_EDIT_BY_CLIENT_STATUS     ='deniedByClient';
    const FINISH_ORDER_BY_WORKER_STATUS     ='finishByWorker';
    const PAID_ORDER_BY_CLIENT_STATUS       ='paidByClient';
    const CANCEL_ORDER_BY_CLIENT_STATUS     ='cancelByClient';
    const CANCEL_ORDER_BY_WORKER_STATUS     ='cancelByWorker';
    const CANCEL_ORDER_BY_ADMIN_STATUS      ='cancelByAdmin';
    const NOT_FOUND_WORKER_STATUS           ='notFoundWorker';
}
