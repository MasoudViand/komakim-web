<?php

namespace App\Http\Controllers;

use App\Category;
use App\OrderPayment;
use App\User;
use App\WorkerProfile;
use function GuzzleHttp\Psr7\_parse_message;
use Illuminate\Http\Request;
use Mockery\Exception;
use MongoDB\BSON\ObjectID;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
//    public function __construct()
//    {
//        $this->middleware('auth');
//    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    function getworkwithusForm(){
        $categories =Category::all();
        $data['categories']=$categories;

       return view('work_with_us')->with($data);
    }

    function registerWorker(Request $request)
    {




        $this->validate($request,[
            'name' => 'required',
            'family' => 'required',
            'nationalCode' => 'required',
            'phoneNumber' => 'required',
            'mobileNumber' => 'required',
            'address' => 'required',
            'birthday' => 'required',
        ]);





        $user = User::where('phone_number',$request['mobileNumber'])->first();


        if ($user){
            $message['error'] = 'این شماره تلفن قبلا ثبت شده است ';
            return redirect()->back()->with($message);
        }

        $user =new User();
        $user->name =$request['name'];
        $user->family =$request['family'];
        $user->email =$request['email'];
        $user->status = 'active';
        $user->password=bcrypt($request['mobileNumber']);
        $user->phone_number=$request['mobileNumber'];
        $user->isCompleted =true;
        $user->role ='worker';
        if ($user->save()){
            $message =$this->_saveProfile($request,$user->id);
        }else
            $message = 'مشکلی برای ذخیره یوزر';

        return redirect()->route('home')->with($message);
    }


    protected function _saveProfile( $request ,$user_id)
    {
        $workerProfile= new WorkerProfile();
        $workerProfile->user_id=new ObjectID($user_id);
        $workerProfile->national_code=$request['nationalCode'];
        $workerProfile->address =$request['address'];
        $workerProfile->home_phone_number = $request['phoneNumber'];
        $workerProfile->birthDay =   \Morilog\Jalali\jDateTime::createDatetimeFromFormat('Y/m/d H:i:s', $request['birthday'].' 00:00:00');
        $workerProfile->fields = $request['fields'];
        $workerProfile->last_education = $request['lastEducation'];
        $workerProfile->marriage_status = $request['marriageStatus'];
        $workerProfile->gender = $request['gender'];
        $workerProfile->another_capability = $request['anotherCapability'];
        $workerProfile->certificates = $request['certificates'];
        $workerProfile->experience = $request['experience'];
        $workerProfile->status ='pending';
        $workerProfile->availability_status ='available';
        $message=null;
        if ($workerProfile->save()){
            $message['success'] ='پروفایل به درستی ذخیره شد';
        }else
            $message['error'] ='مشکلی به وجود امده مجددا تلاش کنید';
        return $message;


    }


}
