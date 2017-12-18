<?php

namespace App\Http\Controllers\Api;

use App\TempToken;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
                $api = new \Kavenegar\KavenegarApi("41415673525664384E6F334A7973504F685A434159773D3D");
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

            if (!$user)
            {

                $user = User::create([
                    'name' => $phoneNumber,
                    'family' => $phoneNumber,
                    'email' => $phoneNumber,
                    'password' => bcrypt($phoneNumber),
                    'phone_number' => ($phoneNumber),
                    'isCompleted'   =>false,
                    'role'          =>$request['user_type']
                ]);
            }


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

        $profile =false;

        $user = User::where('phone_number',$phoneNumber)->first();

        if ($user->isCompleted)
        {
            $profile['name'] =$user->name;
            $profile['family'] =$user->family;
            $profile['phone_number'] =$user->phone_number;
            $profile['email'] =$user->email;
            $profile['role'] =$user->role;
        }


           $guzzleresult =json_decode((string) $response->getBody(), true);


            $result =[];
            $result['token_type']= $guzzleresult['token_type'];
            $result['expires_in']= $guzzleresult['expires_in'];
            $result['access_token']= $guzzleresult['access_token'];
            $result['refresh_token']=$guzzleresult['refresh_token'];
            $result['profile'] =$profile;




            return response()->json($result);
        }

    }
    function verifyWorkerCode(Request $request)
    {
        $accessToken='eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImNmNDAwOWU0ZjE0ZGRhMjA2ZDU0Y2E3NjUxZjRiNDAyMjQ2NDQyMjcyZjcwOWFjY2FmMTE3YTY1YmY3NWM1MTY1NjY2NTY1ZmYwYzVkYjBlIn0.eyJhdWQiOiI1YTM0ZmUxYTk3OGVmNDU1ZmQyODAwOTQiLCJqdGkiOiJjZjQwMDllNGYxNGRkYTIwNmQ1NGNhNzY1MWY0YjQwMjI0NjQ0MjI3MmY3MDlhY2NhZjExN2E2NWJmNzVjNTE2NTY2NjU2NWZmMGM1ZGIwZSIsImlhdCI6MTUxMzQ5MjgxNywibmJmIjoxNTEzNDkyODE3LCJleHAiOjE1NDUwMjg4MTcsInN1YiI6IjVhMzYxMDQwOTc4ZWY0MjI0ODIwNWY5NyIsInNjb3BlcyI6W119.nmEfOqNfA2s39GiwcArz5y-XA90j2fcl5SvmozBcEyAUyZmSi0aWU0ELOfMLoE0qwlNisNpWKjjAL9vzk9xRoztcN7_JmL8cIgdHOrk0Z0wGEQ9mH78CVOxFFZ2CCZrCXDdmgOSSO29i047Y1atDVsxafzy2X7GbXUkSmG6Kdg2itSvwd6EEKzRc2fOMBF_p3WKp2CDR8KgQi_yiciYwWYft_JMjDqQ9-zP5WhAKQjbNcQpxd0RS0IUJI_tsu1Cl2mNlhbmt3sPITVrVSjT5isFuzxVpiMhiDoZyM2oUQpl_M0FsxDioCslwla7qQa0xRh_rG1kxoRyrlfUKcN2MrYn5I6Sy7kNZjjmMMJHFkCTXLbCwdcYJXfWcHno2Ot1zSWl5lJOsjl-PvSdodu8VS5-95QtLfzicDyHe4CR8kQPuYgw_j-HQ9hG-YYgjJqRnsPZy2dqHQVDq6oMumaiZ4EfCk6KrnLnpzsITwZq_69opJhL2_m7KcEKrDgxUJkYGZ_A4bekXDyt_r5CpTYO2mC0GcBxhf7hcFI5EUXNIOjhFRDK9u30sX9hJKwBIKhGEvnthLfc_gez7n7ehVJ-IRoH2fVnSyhsUVVPbtEWWdj4w5smjeKTB3dIlA0c1I87LvRxAPdgb6RttQozzGPUI4ijvSSSj_7X8eWtaxFqBHWI';
        $client = new GuzzleHttp\Client;
        $response = $client->request('GET', 'http://127.0.0.1/web-server/public/api/user', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$accessToken,
            ],
        ]);
        dd($response);


        $validator = Validator::make($request->all(), [
            'validation_code' => 'required',
            'sms_code' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()])->setStatusCode(417);
        }


        $tempToken = TempToken::where('sms_code',$request['sms_code'])->where('random_string_token',$request['validation_code'])->first();



        if ($tempToken)
        {

            $phoneNumber =$tempToken->phone_number;


            $user = User::where('phone_number',$phoneNumber)->first();

            if (!$user)
            {

                $user = User::create([
                    'name' => $phoneNumber,
                    'family' => $phoneNumber,
                    'email' => $phoneNumber,
                    'password' => bcrypt($phoneNumber),
                    'phone_number' => ($phoneNumber),
                    'isCompleted'   =>false,
                ]);
            }


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

            $profile =false;

            $user = User::where('phone_number',$phoneNumber)->first();

            if ($user->isCompleted)
            {
                $profile['name'] =$user->name;
                $profile['family'] =$user->family;
                $profile['phone_number'] =$user->phone_number;
                $profile['email'] =$user->email;
            }


            $guzzleresult =json_decode((string) $response->getBody(), true);


            $result =[];
            $result['token_type']= $guzzleresult['token_type'];
            $result['expires_in']= $guzzleresult['expires_in'];
            $result['access_token']= $guzzleresult['access_token'];
            $result['refresh_token']=$guzzleresult['refresh_token'];
            $result['profile'] =$profile;




            return response()->json($result);
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
}
