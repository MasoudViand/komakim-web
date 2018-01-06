<?php

namespace App\Http\Controllers\Api;

use App\TempToken;
use App\User;
use App\WorkerProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use MongoDB\BSON\ObjectID;
use Validator;
use GuzzleHttp;


class PhoneNumberController extends Controller
{

    function receiveCode(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phoneNumber' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()])->setStatusCode(417);
        }


        $user = User::where('phone_number', $request['phoneNumber'])->first();


        if (!$user)
        {
             return $this->_sendSms($request);
        }else
        {
            if ($user->status =='inactive')
            {
                return response()->json(['errors'=>'user is inactive'])->setStatusCode(402);
            }

            if ($user->role =='worker')
            {
                $workerProfile = WorkerProfile::where('user_id' , new ObjectID($user->id))->first();

                if (!$workerProfile)
                {
                    return response()->json(['errors'=>'user have no worker profile'])->setStatusCode(402);
                }

                if ($workerProfile->status=='pending')
                {
                    return response()->json(['errors'=>'worker must be wait fo acceptance of system '])->setStatusCode(402);

                }


                if ($workerProfile->status=='reject')
                {
                    return response()->json(['errors'=>'authority of worker rejected'])->setStatusCode(402);
                }

                 return $this->_sendSms($request);
            }

            if ($user->role=='client')
            {
                 return $this->_sendSms($request);
            }
            return response()->json(['errors'=>'type of user not defined'])->setStatusCode(402);

        }


    }
    function verifyCode(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'validation_code' => 'required',
            'sms_code' => 'required',
            'user_type' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()])->setStatusCode(417);

        }
        if ((!($request['user_type']=='worker'))and (!($request['user_type']=='client')) )
            return response()->json(['errors'=>'user_type is invalid'])->setStatusCode(417);




        $tempToken = TempToken::where('sms_code',$request['sms_code'])->where('random_string_token',$request['validation_code'])->first();



        if ($tempToken)
            {

            $phoneNumber =$tempToken->phone_number;


            $user = User::where('phone_number',$phoneNumber)->first();

            if (!$user and $request['user_type']=='client')
            {
                $user = new User();
                $user->phone_number=$phoneNumber;
                $user->password = bcrypt($phoneNumber);
                $user->isCompleted =false;
                $user->status = 'active';
                $user->role    =$request['user_type'];

                if (!$user->save())
                {
                    //Todo lets show error

                }


            }
            if (!$user)
            {
                $tempToken->delete();
                return response()->json(['errors'=>'you must first register in admin panel'])->setStatusCode(402);
            }

            if (!($user->role==$request['user_type']))
                return response()->json(['errors'=>'you not register'])->setStatusCode(402);




                $response =$this->_getAccessToken($phoneNumber);
        $profile =false;

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
        }

        return response()->json(['errors'=>'your sms or validation code is incorrect'])->setStatusCode(402);



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
                return response()->json(['errors'=>"phone is not accessible"])->setStatusCode(412);


            }
            catch(\Kavenegar\Exceptions\HttpException $e){
                // در زمانی که مشکلی در برقرای ارتباط با وب سرویس وجود داشته باشد این خطا رخ می دهد
                echo $e->errorMessage();
                TempToken::where('phone_number',$tempToken->phone_number)->delete();
                return response()->json(['errors'=>"kave negar service not response"])->setStatusCode(500);
            }

        }else
        {
            return response()->json(['errors'=>"service not response"])->setStatusCode(500);

        }



    }

    function _getAccessToken($phoneNumber)
    {
        $http = new GuzzleHttp\Client;

        $response = $http->post('http://127.0.0.1/web-server/public/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => '5a34fe1a978ef455fd280094',
                'client_secret' => 'fBHnxIIy9ckSYpARFbwmreC3gRUr0mN2siGg2VmT',
                'username' => $phoneNumber,
                'password' => $phoneNumber,
                'scope' => '',
            ],
        ]);

        return $response;
    }
}
