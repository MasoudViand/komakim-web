<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use App\MongoDbUser as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable;


    const ADMIN_ROLE ='admin';
    const OPERATOR_ROLE ='operator';
    const FINANCIAL_ROLE ='financial';


    protected $guard = 'admin';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
