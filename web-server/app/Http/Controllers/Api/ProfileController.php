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

        /* $user =$request->user()
         *
         */
        $user =User::find('5a361040978ef42248205f97');

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
            return response()->json(['errors'=>"something not wright"])->setStatusCode(417);


        }




        /**@var \App\User */
        $user =$request->user();

        if ($user->isCompleted)
        {

        }
    }
}
