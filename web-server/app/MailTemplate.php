<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class MailTemplate extends Eloquent
{
    protected $collection='email_templates';
}
