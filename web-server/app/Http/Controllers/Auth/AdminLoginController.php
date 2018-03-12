<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AdminLoginController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
       $this->validate($request,[
           'email' => 'required|email',
           'password' => 'required|min:6'
       ]);

       if (Auth::guard('admin')->attempt(['email' =>$request->email ,'password' => $request->password],$request->remember)){
           return redirect()->intended(route('admin.dashboard'));
       }

       return redirect()->back()->withInput($request->only('email' ,'remember'));
    }

    public function logout()
    {
        $user =Auth::user();
        $user->token_2fa_expiry=null;
        $user->token_2fa=null;

        Auth::logout();
        Session::flush();
        return redirect('admin/login');
    }

    public function authenticated()
    {
        $user = Auth::user();
        $user->token_2fa_expiry = \Carbon\Carbon::now();
        $user->save();
        return redirect('/admin');
    }
}
