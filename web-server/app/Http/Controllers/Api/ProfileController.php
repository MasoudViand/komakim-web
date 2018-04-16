<?php

namespace App\Http\Controllers\Api;

use App\Order;
use App\OrderStatusRevision;
use App\Setting;
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
            return response()->json(['errors'=>'fcm_token is require'])->setStatusCode('417');

        }

        $user = $request->user();

        $user->fcm_token=$request->input('fcm_token');
        if ($user->save())
        {
            unset($user['isCompleted']);unset($user['role']);unset($user['updated_at']);unset($user['created_at']);unset($user['fcm_token']);

            return response()->json(['profile'=>$user]);

        }
        else{

            return response()->json(['errors'=>'something wrong! try again'])->setStatusCode(409);
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
            return response()->json(['errors' =>'account_number must be send'])->setStatusCode(417);

        }
        $workerProfile = WorkerProfile::where('user_id',new ObjectID($request->user()->id))->first();

        $workerProfile->account_number = $request->input('account_number');


        if ($workerProfile->save())
            return response()->json(['workerProfile',$workerProfile]);
        else
            return response(['errors' =>'internal server errors'])->setStatusCode(500);


    }

    function changeAvailibilitySattis(Request $request)
    {


        if (!$request->has('availability_status'))
        {
            return response()->json(['errors' =>'availability_status must be send'])->setStatusCode(417);

        }

        if ($request->input('availability_status')!=WorkerProfile::WORKER_AVAILABLE_STATUS and $request->input('availability_status')!=WorkerProfile::WORKER_UNAVAILABLE_STATUS )
            return response()->json(['errors' =>'please send correct value'])->setStatusCode(417);


        $workerProfile = WorkerProfile::where('user_id',new ObjectID($request->user()->id))->first();

        $workerProfile->availability_status = $request->input('availability_status');


        if ($workerProfile->save())
            return response()->json(['workerProfile',$workerProfile]);
        else
            return response(['errors' =>'internal server errors'])->setStatusCode(500);


    }


    function registerLocation(Request $request)
    {

        if (!$request->has('latitude')or!$request->has('longitude'))
        {
            return response()->json(['errors'=>'latitude and longitude is require'])->setStatusCode('417');

        }



        $user = $request->user();

        if ($user->role!=User::WORKER_ROLE)
        {
            return response()->json(['errors'=>'user must have worker role'])->setStatusCode('417');

        }





        $location =new \stdClass();
        $location->type ="Point";
        $coordinates =[(double)$request->input('longitude'),(double)$request->input('latitude')];
        $location->coordinates=$coordinates;




        $workerProfile = WorkerProfile::where('user_id',new ObjectID($user->id))->first();

        if (!$workerProfile)
            return response()->json(['errors'=>'worker profile not exist'])->setStatusCode('417');


        $workerProfile->location =$location;


        if ($workerProfile->save())
        {
            unset($user['isCompleted']);unset($user['role']);unset($user['updated_at']);unset($user['created_at']);unset($user['fcm_token']);

            return response()->json(['profile'=>$user,'location'=>$location]);

        }
        else{

            return response()->json(['errors'=>'something wrong! try again'])->setStatusCode(409);
        }


    }

    function initialize(Request $request)
    {

        if ($request->input('user_type')==User::WORKER_ROLE)
        {
            $versionModel = Setting::where('type', 'app_version')->where('app_type', User::WORKER_ROLE)->first();
            unset($versionModel->created_at);
            unset($versionModel->updated_at);
            unset($versionModel->_id);
            unset($versionModel->type);

            $initialize['version'] = $versionModel;
            $userModel = $request->user();

            if (!$userModel)
            {
                return response()->json(['initialize'=>$initialize])->setStatusCode(401);
            }



            if (!$userModel->isCompleted)
                return response()->json(['errors'=>"پروفایل شما تکمیل نیست",'initialize'=>$initialize])->setStatusCode(421);
            if ($userModel->status==User::DISABLE_USER_STATUS)
                return response()->json(['errors'=>"کاربری شما غیر فعال شده.لطفا با پشتیبانی تماس بگیرید",'initialize'=>$initialize])->setStatusCode(422);

            if ($request->has('fcm_token'))
            {
                $userModel->fcm_token=$request->input('fcm_token');
                $userModel->save();
            }

            $worker_profile = WorkerProfile::where('user_id',new ObjectID($userModel->id))->first();

            if (!$worker_profile)
                return response()->json(['errors'=>"پروفایل خدمه تکمیل نشده.لطفا با پشتیبانی تماس بگیرید"])->setStatusCode(423);
            if ($worker_profile->status==WorkerProfile::WORKER_PENDING_STATUS)
                return response()->json(['errors'=>"کاربری شما در مرحله تاییدیه میباشد.لطفا با پشتیبانی تماس بگیرید"])->setStatusCode(424);
            if ($worker_profile->status==WorkerProfile::WORKER_REJECT_STATUS)
                return response()->json(['errors'=>"کاربری شما رد شده است.لطفا با پشتیبانی تماس بگیرید"])->setStatusCode(425);


            $user['phone_number'] = $userModel->phone_number;
            $user['name'] = $userModel->name;
            $user['family'] = $userModel->family;

            $wallet =Wallet::where('user_id',new ObjectID($userModel->id))->first();
            if ($wallet)
                $user['wallet']=$wallet->amount;
            else
                $user['wallet']=0;


            $hasActiveOrder=false;

            if ($worker_profile->has_active_order)
                $hasActiveOrder=true;
            $initialize['hasActiveOrder']=$hasActiveOrder;




            return response()->json(['initialize'=>$initialize,'user'=>$user,'worker_profile'=>$worker_profile]);




        }elseif ($request->input('user_type')==User::CLIENT_ROLE)
        {


            $versionModel = Setting::where('type', 'app_version')->where('app_type', User::CLIENT_ROLE)->first();
            unset($versionModel->created_at);
            unset($versionModel->updated_at);
            unset($versionModel->_id);
            unset($versionModel->type);



            $initialize['version'] = $versionModel;
            $userModel = $request->user();

            if (!$userModel)
            {
                return response()->json(['initialize'=>$initialize])->setStatusCode(401);
            }

            if (!$userModel->isCompleted)
                return response()->json(['errors'=>"پروفایل شما تکمیل نیست",'initialize'=>$initialize])->setStatusCode(421);
            if ($userModel->status==User::DISABLE_USER_STATUS)
                return response()->json(['errors'=>"کاربری شما غیر فعال شده.لطفا با پشتیبانی تماس بگیرید",'initialize'=>$initialize])->setStatusCode(422);
            if ($request->has('fcm_token'))
            {
                $userModel->fcm_token=$request->input('fcm_token');
                $userModel->save();
            }

            $user['phone_number'] = $userModel->phone_number;
            $user['name'] = $userModel->name;
            $user['family'] = $userModel->family;

            $wallet =Wallet::where('user_id',new ObjectID($userModel->id))->first();

            if ($wallet)
                $user['wallet']=$wallet->amount;
            else
                $user['wallet']=0;
            $activeStatus=[OrderStatusRevision::WAITING_FOR_WORKER_STATUS,OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS,OrderStatusRevision::START_ORDER_BY_WORKER_STATUS,OrderStatusRevision::FINISH_ORDER_BY_WORKER_STATUS,OrderStatusRevision::EDIT_BY_WORKER_STATUS];

            $countOrders = Order::whereIn('status',$activeStatus)->where('user_id',new ObjectID($request->user()->id))->count();

            $hasActiveOrder=false;

            if ($countOrders>0)
                $hasActiveOrder=true;
            $initialize['hasActiveOrder']=$hasActiveOrder;

            return response()->json(['initialize'=>$initialize,'user'=>$user]);
        }else
            return response()->json(['errors'=>"user_type is not defined"])->setStatusCode(417);

    }

    public function editprofile( Request $request)
    {

        $user =$request->user();

        if ($request->has('name'))
                $user->name=$request->input('name');
        if ($request->has('family'))
                $user->family=$request->input('family');
        if ($request->has('email'))
                $user->email=$request->input('email');

        if ($user->save())
        {
            return response()->json(['profile'=>$user]);

        }else
            return response()->json(['errors'=>"something not right"])->setStatusCode(417);



    }
}
