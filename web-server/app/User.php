<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    const WORKER_ROLE ='worker';
    const CLIENT_ROLE ='client';


    const DISABLE_USER_STATUS ='disabled';
    const ENABLE_USER_STATUS ='enable';
    use Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','family','isCompleted', 'email', 'password','phone_number','status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function workerProfile()
    {
        return $this->hasOne('App\WorkerProfile');
    }
}
