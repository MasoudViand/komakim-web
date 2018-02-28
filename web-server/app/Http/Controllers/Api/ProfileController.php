<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Wallet;
use App\WorkerProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;
use MongoDB\BSON\ObjectID;
use Validator;


class ProfileController extends Controller
{

    function addprofile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'family' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()])->setStatusCode(417);
        }

         $user =$request->user();


        if ($user->isCompleted)
            return response()->json(['errors'=>"profile previously completed"])->setStatusCode(417);


        $user->name =$request['name'];
        $user->family =$request['family'];
        if ($request['email'])
            $user->email =$request['email'];
        $user->isCompleted=true;

        if ($user->save())
        {
            return response()->json(['profile'=>$user]);



        }else{
            return response()->json(['errors'=>"something not right"])->setStatusCode(417);


        }


    }

    function registerFcmToken(Request $request)
    {
        if (!$request->has('fcm_token'))
        {
            return response()->json(['error'=>'fcm_token is require'])->setStatusCode('417');

        }

        $user = $request->user();

        $user->fcm_token=$request->input('fcm_token');
        if ($user->save())
        {
            unset($user['isCompleted']);unset($user['role']);unset($user['updated_at']);unset($user['created_at']);unset($user['fcm_token']);

            return response()->json(['profile'=>$user]);

        }
        else{

            return response()->json(['error'=>'something wrong! try again'])->setStatusCode(409);
        }


    }

    function getprofileInfo(Request $request)
    {

        $user = User::find($request->user()->id);
        $wallet = Wallet::where('user_id',new ObjectID($request->user()->id))->first();
        if ($wallet)
            $wallet = $wallet->amount;
        else
            $wallet=0;

        $user->wallet =$wallet;

        if ($user->role==User::WORKER_ROLE)
        {


            $workerProfile = WorkerProfile::where('user_id',new ObjectID($user->id))->first();
            $url_image =  '/images/workers/profile-default-male.png';
            if (file_exists((public_path('images/workers') . '/' . $workerProfile->id) . '.png')) $url_image = ('/images/workers') . '/' . $workerProfile->id . '.png';
            if (file_exists((public_path('images/workers') . '/' . $workerProfile->id) . '.jpg')) $url_image = ('/images/workers') . '/' . $workerProfile->id . '.jpg';
            if (file_exists((public_path('images/workers') . '/' . $workerProfile->id) . '.jpeg')) $url_image = ('/images/workers') . '/' . $workerProfile->id . '.jpeg';

            $url_image=URL::to('/') .''.$url_image;

            $workerProfile->url_image = $url_image;

            unset($user->status);unset($user->isCompleted);unset($user->role);unset($user->updated_at);unset($user->created_at);unset($user->fcm_token);
            return response()->json(['user'=>$user,'worker_profile'=>$workerProfile]);


        }
        unset($user->status);unset($user->isCompleted);unset($user->role);unset($user->updated_at);unset($user->created_at);unset($user->fcm_token);

        return response()->json(['user'=>$user]);
    }
    function addAcounNumberToWorkerProfile(Request $request)
    {


        if (!$request->has('account_number'))
        {
            return response()->json(['error' =>'account_number must be send'])->setStatusCode(417);

        }
        $workerProfile = WorkerProfile::where('user_id',new ObjectID($request->user()->id))->first();

        $workerProfile->account_number = $request->input('account_number');


        if ($workerProfile->save())
            return response()->json(['workerProfile',$workerProfile]);
        else
            return response(['error' =>'internal server error'])->setStatusCode(500);


    }

    function changeAvailibilitySattis(Request $request)
    {


        if (!$request->has('availability_status'))
        {
            return response()->json(['error' =>'availability_status must be send'])->setStatusCode(417);

        }

        if ($request->input('availability_status')!=WorkerProfile::WORKER_AVAILABLE_STATUS and $request->input('availability_status')!=WorkerProfile::WORKER_UNAVAILABLE_STATUS )
            return response()->json(['error' =>'please send correct value'])->setStatusCode(417);


        $workerProfile = WorkerProfile::where('user_id',new ObjectID($request->user()->id))->first();

        $workerProfile->availability_status = $request->input('availability_status');


        if ($workerProfile->save())
            return response()->json(['workerProfile',$workerProfile]);
        else
            return response(['error' =>'internal server error'])->setStatusCode(500);


    }


    function registerLocation(Request $request)
    {

        if (!$request->has('latitude')or!$request->has('longitude'))
        {
            return response()->json(['error'=>'latitude and longitude is require'])->setStatusCode('417');

        }



        $user = $request->user();

        if ($user->role!=User::WORKER_ROLE)
        {
            return response()->json(['error'=>'user must have worker role'])->setStatusCode('417');

        }





        $location =new \stdClass();
        $location->type ="Point";
        $coordinates =[(double)$request->input('latitude'),(double)$request->input('longitude')];
        $location->coordinates=$coordinates;




        $workerProfile = WorkerProfile::where('user_id',new ObjectID($user->id))->first();

        if (!$workerProfile)
            return response()->json(['error'=>'worker profile not exist'])->setStatusCode('417');


        $workerProfile->location =$location;


        if ($workerProfile->save())
        {
            unset($user['isCompleted']);unset($user['role']);unset($user['updated_at']);unset($user['created_at']);unset($user['fcm_token']);

            return response()->json(['profile'=>$user,'location'=>$location]);

        }
        else{

            return response()->json(['error'=>'something wrong! try again'])->setStatusCode(409);
        }


    }

    function initialize(Request $request)
    {
        if (!$request->has('user_type'))
        {
            return response()->json(['error'=>'user_type is require'])->setStatusCode(417);

        }


    }
}
