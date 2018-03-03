<?php

namespace App\Http\Middleware;

use App\Setting;
use App\User;
use App\WorkerProfile;
use Closure;
use MongoDB\BSON\ObjectID;

class CheckUserHasAccessToken
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
        if (!$request->has('user_type'))
        {
            return response()->json(['error'=>'user_type is require'])->setStatusCode(417);

        }
           if (!$request->header('Authorization'))
           {
               if ($request->input('user_type')==User::WORKER_ROLE)
               {
                   $versionModel=Setting::where('type','app_version')->where('app_type',User::WORKER_ROLE)->first();
                   unset($versionModel->created_at);
                   unset($versionModel->updated_at);
                   unset($versionModel->_id);
                   unset($versionModel->type);

                   $initialize['version']=$versionModel;
                   return response()->json(['initialize'=>$initialize])->setStatusCode(401);
               }elseif ($request->input('user_type')==User::CLIENT_ROLE)
               {

                   $versionModel=Setting::where('type','app_version')->where('app_type',User::CLIENT_ROLE)->first();
                   unset($versionModel->created_at);
                   unset($versionModel->updated_at);
                   unset($versionModel->_id);
                   unset($versionModel->type);

                   $initialize['version']=$versionModel;
                   return response()->json(['initialize'=>$initialize])->setStatusCode(401);

               }else
                   return response()->json(['error'=>'user_type not defined'])->setStatusCode(401);


           }


        return $next($request);
    }
}
