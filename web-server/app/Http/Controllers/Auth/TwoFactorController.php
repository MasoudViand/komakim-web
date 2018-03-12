<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use MongoDB\BSON\UTCDateTime;

class TwoFactorController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin')->except('showTwoFactorForm');
    }


    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            '2fa' => 'required',
        ]);


        if($request->input('2fa') == Auth::user()->token_2fa){

            $user = Auth::user();
            $token_2fa_expiry = \Carbon\Carbon::now()->addMinutes(config('session.lifetime'));
            $token_2fa_expiry = new UTCDateTime($token_2fa_expiry);
            $user->token_2fa_expiry =$token_2fa_expiry;
            $user->save();
            return redirect('/admin');
        } else {

            return redirect('/ark-2fa')->with('message', 'Incorrect code.');
        }
    }

    public function showTwoFactorForm()
    {

        return view('auth.two_factor');
    }





}
