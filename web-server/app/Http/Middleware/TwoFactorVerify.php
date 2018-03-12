<?php

namespace App\Http\Middleware;

use App\Admin;
use Closure;
use Illuminate\Support\Facades\Auth;

class TwoFactorVerify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (!is_null($user->token_2fa_expiry))
        {
            $token_2fa_expiry = $user->token_2fa_expiry->toDateTime();
            $token_2fa_expiry->setTimeZone(new \DateTimeZone('Asia/Tehran'));


            if($token_2fa_expiry > \Carbon\Carbon::now()){


                return $next($request);
            }
        }



        $user->token_2fa = mt_rand(10000,99999);
        $user->save();
        // This is the twilio way
        //Twilio::message($user->phone_number, 'Two Factor Code: ' . $user->token_2fa);
        try {


            $sender = "100065995";
            $receptor = $user->phone_number;
            $message = 'Two Factor Code: ' . $user->token_2fa;
            $api = new \Kavenegar\KavenegarApi("41592B50794462786F746C68364338573231783474673D3D");
            $api->Send($sender, $receptor, $message);



        } catch(\Kavenegar\Exceptions\ApiException $e){



        }
        catch(\Kavenegar\Exceptions\HttpException $e){
            // در زمانی که مشکلی در برقرای ارتباط با وب سرویس وجود داشته باشد این خطا رخ می دهد

        }

        return redirect('/2fa');


    }
}
