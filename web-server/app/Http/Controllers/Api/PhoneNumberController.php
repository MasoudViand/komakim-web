<?php

namespace App\Http\Controllers\Api;

use App\Node;
use App\OrderStatusRevision;
use App\TempToken;
use App\User;
use App\Wallet;
use App\WorkerProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;
use Validator;
use GuzzleHttp;


class PhoneNumberController extends Controller
{


    function receiveCode(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phoneNumber' => 'required',
            'user_type' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()])->setStatusCode(417);
        }


        if ($request->input('user_type')==User::CLIENT_ROLE)
        {
            $user = User::where('phone_number', $request['phoneNumber'])->first();
            if (!$user )
            {
                return $this->_sendSms($request);
            }


            if ($user->role !=User::CLIENT_ROLE)
            {
                return response()->json(['error'=>'کاربری با این شماره در سامانه وجود دارد'])->setStatusCode(420);

            }


            if ($user->status ==User::DISABLE_USER_STATUS)
            {
                return response()->json(['error'=>'کاربر غیر فعال شده است'])->setStatusCode(420);
            }

            return $this->_sendSms($request);

        }elseif($request->input('user_type')==User::WORKER_ROLE)
        {
            $user = User::where('phone_number', $request['phoneNumber'])->first();

            if (!$user )
            {
                return response()->json(['error'=>'باید ابندا فرم ثبت نام توسط خدمه تکمیل گردد'])->setStatusCode(420);

            }
            if ($user->role !=User::WORKER_ROLE)
            {
                return response()->json(['error'=>'کاربری با این شماره در سامانه وجود دارد'])->setStatusCode(420);

            }


            if ($user->status ==User::DISABLE_USER_STATUS)
            {
                return response()->json(['error'=>'کاربر غیر فعال شده است'])->setStatusCode(420);
            }

            $workerProfile = WorkerProfile::where('user_id' , new ObjectID($user->id))->first();
            if (!$workerProfile)
            {
                return response()->json(['error'=>'پروفایل کاربر تشکیل نشده است'])->setStatusCode(420);
            }

            if ($workerProfile->status==WorkerProfile::WORKER_PENDING_STATUS)
            {
                return response()->json(['error'=>'درخواست کاربر هنوز مورد بررسی قرار نگرفته'])->setStatusCode(420);

            }


            if ($workerProfile->status==WorkerProfile::WORKER_REJECT_STATUS)
            {
                return response()->json(['error'=>'درخواست خدمه مورد قبول واقع نشده'])->setStatusCode(402);
            }

            return $this->_sendSms($request);

        }
        else
            return response()->json(['error'=>'نوع کاربر ناشناخته می باشد'])->setStatusCode(402);

    }
    function verifyCode(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'validation_code' => 'required',
            'sms_code' => 'required',
            'user_type' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()])->setStatusCode(417);

        }

        if ((!($request['user_type']==User::WORKER_ROLE))and (!($request['user_type']==User::CLIENT_ROLE)) )
            return response()->json(['error'=>'user_type is invalid'])->setStatusCode(417);


        $tempToken = TempToken::where('sms_code',$request['sms_code'])->where('random_string_token',$request['validation_code'])->first();





        if ($tempToken)
            {
                if ($tempToken->user_type!=$request->input('user_type'))
                    return response()->json(['error'=>'کد ناشناخته'])->setStatusCode(402);


            $phoneNumber =$tempToken->phone_number;


            $user = User::where('phone_number',$phoneNumber)->first();


            if (!$user)
            {
                if ($request['user_type']==User::CLIENT_ROLE)
                {
                    $user = new User();
                    $user->phone_number=$phoneNumber;
                    $user->password = bcrypt($phoneNumber);
                    $user->isCompleted =false;
                    $user->status = User::ENABLE_USER_STATUS;
                    $user->role    =$request['user_type'];

                    if (!$user->save())
                    {
                        //Todo lets show error

                    }

                }else
                {
                    $tempToken->delete();
                    return response()->json(['error'=>'شما باید ابتدا از طریق فرم سایت ثبت نام اولیه را انجام دهید'])->setStatusCode(420);
                }

                }


        $response =$this->_getAccessToken($phoneNumber);
        $profile =null;

        if ($user->isCompleted)
        {
            $profile['name'] =$user->name;
            $profile['family'] =$user->family;
            $profile['phone_number'] =$user->phone_number;
            $profile['email'] =$user->email;
            $profile['status'] =$user->status;
            $profile['role'] =$user->role;
        }


           $guzzleresult =json_decode((string) $response->getBody(), true);


            $result =[];
            $result['token_type']= $guzzleresult['token_type'];
            $result['expires_in']= $guzzleresult['expires_in'];
            $result['access_token']= $guzzleresult['access_token'];
            $result['refresh_token']=$guzzleresult['refresh_token'];
            $result['profile'] =$profile;
            $tempToken->delete();


            return response()->json($result);
        }else{
            return response()->json(['error'=>'کد ناشناخته'])->setStatusCode(402);
        }
        
    }


    function GeraHash($qtd){
        $Caracteres = 'ABCDEFGHIJKLMOPQRSTUVXWYZ0123456789';
        $QuantidadeCaracteres = strlen($Caracteres);
        $QuantidadeCaracteres--;

        $Hash=NULL;
        for($x=1;$x<=$qtd;$x++){
            $Posicao = rand(0,$QuantidadeCaracteres);
            $Hash .= substr($Caracteres,$Posicao,1);
        }

        return $Hash;
    }

    function _sendSms($request){

        TempToken::where('phone_number',$request['phoneNumber'])->delete();

        $tempToken =new TempToken();
        $tempToken->phone_number = $request['phoneNumber'];
        $tempToken->user_type = $request['user_type'];
        $tempToken->sms_code = (string)rand(1000,9999);
        $tempToken->random_string_token =$this->GeraHash(25);


        if ($tempToken->save())
        {


            try {


                $sender = "100065995";
                $receptor = $tempToken->phone_number;
                $message = $tempToken->sms_code;
                $api = new \Kavenegar\KavenegarApi("41592B50794462786F746C68364338573231783474673D3D");
                $api->Send($sender, $receptor, $message);
                return response()->json(['validation_code'=>$tempToken->random_string_token]);


            } catch(\Kavenegar\Exceptions\ApiException $e){
                dd($e);
                // در صورتی که خروجی وب سرویس 200 نباشد این خطا رخ می دهد

                TempToken::where('phone_number',$tempToken->phone_number)->delete();
                return response()->json(['error'=>"تلفن در دسترس نیست"])->setStatusCode(420);


            }
            catch(\Kavenegar\Exceptions\HttpException $e){
                // در زمانی که مشکلی در برقرای ارتباط با وب سرویس وجود داشته باشد این خطا رخ می دهد
                echo $e->errorMessage();
                TempToken::where('phone_number',$tempToken->phone_number)->delete();
                return response()->json(['error'=>"kave negar service not response"])->setStatusCode(500);
            }

        }else
        {
            return response()->json(['error'=>"service not response"])->setStatusCode(500);

        }



    }

    function _getAccessToken($phoneNumber)
    {
        $http = new GuzzleHttp\Client;
       // dd(URL::to('/').'/oauth/token');

        $response = $http->post('http://127.0.0.1/oauth/token', [
        //$response = $http->post(URL::to('/').'/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
              //  'client_id' => '5a34fe1a978ef455fd280094',// local
                'client_id' => '5a8d36aa71d2f757f110c834',   //servertest
              //  'client_secret' => 'fBHnxIIy9ckSYpARFbwmreC3gRUr0mN2siGg2VmT',// local
                'client_secret' => 'X48ILgXNuhJ8HL0cmDvLfu4cOPQ41dMJsPepoYzP', //server test
                'username' => $phoneNumber,
                'password' => $phoneNumber,
                'scope' => '',
            ],
        ]);

        return $response;

    }
}
