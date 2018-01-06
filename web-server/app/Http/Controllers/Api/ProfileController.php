<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
       /// dd($user);
        //$user =User::find('5a361040978ef42248205f97');

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
        unset($user->status);unset($user->isCompleted);unset($user->role);unset($user->updated_at);unset($user->created_at);unset($user->fcm_token);

        return response()->json(['user'=>$user]);
    }
}
