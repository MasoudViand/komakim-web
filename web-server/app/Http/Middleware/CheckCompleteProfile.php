<?php

namespace App\Http\Middleware;

use App\User;
use App\WorkerProfile;
use Closure;
use MongoDB\BSON\ObjectID;

class CheckCompleteProfile
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
            $user = $request->user();

            if (!$user->isCompleted)
                return response()->json(['errors'=>"پروفایل شما تکمیل نیست"])->setStatusCode(421);
            if ($user->status==User::DISABLE_USER_STATUS)
                return response()->json(['errors'=>"کاربری شما غیر فعال شده.لطفا با پشتیبانی تماس بگیرید"])->setStatusCode(422);
            if ($user->role==User::WORKER_ROLE)
            {
                $worker_profile = WorkerProfile::where('user_id',new ObjectID($user->id))->first();
                if (!$worker_profile)
                    return response()->json(['errors'=>"پروفایل خدمه تکمیل نشده.لطفا با پشتیبانی تماس بگیرید"])->setStatusCode(423);
                if ($worker_profile->status==WorkerProfile::WORKER_PENDING_STATUS)
                    return response()->json(['errors'=>"کاربری شما در مرحله تاییدیه میباشد.لطفا با پشتیبانی تماس بگیرید"])->setStatusCode(424);
                if ($worker_profile->status==WorkerProfile::WORKER_REJECT_STATUS)
                    return response()->json(['errors'=>"کاربری شما رد شده است.لطفا با پشتیبانی تماس بگیرید"])->setStatusCode(425);

            }

        return $next($request);
    }
}
