<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class FinancialReport extends Eloquent
{
    protected $collection ="financial_reports";
}
