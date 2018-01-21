<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/admin/user/filter',
        '/admin/order/filter',
        'pay/callback',
        'admin/financial/filter',
        'admin/financial/weekly',
        'admin/financial/daily',
        '/admin/setting/radius/edit',
        '/admin/setting/commission/edit',
        '/admin/settle/done',
        '/admin/settle/export/scv',
    ];
}
