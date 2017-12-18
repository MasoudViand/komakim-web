<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\WorkerProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    function index(){
        $users =User::paginate(15);

        $data['users']=$users;

        return view('admin.pages.user.listUser')->with($data);
    }

    function showEditUserForm($user_id)
    {
        $user = User::find($user_id);

        $workerProfile=false;
        if ($user->role=='worker')
        {
            $workerProfile=WorkerProfile::where('user_id',$user_id)->first();
            $filepath=null;
            if (file_exists((public_path('images/workers').'/'.$workerProfile->id).'.png'))
                $filepath=('images/workers').'/'.$workerProfile->id.'.png';
            if (file_exists((public_path('images/workers').'/'.$workerProfile->id).'.jpg'))
                $filepath=('images/workers').'/'.$workerProfile->id.'.jpg';
            if (file_exists((public_path('images/workers').'/'.$workerProfile->id).'.jpeg'))
                $filepath=('images/workers').'/'.$workerProfile->id.'.jpeg';


            $data['filepath']=$filepath;


            $date = \Morilog\Jalali\jDateTime::strftime('Y-m-d', strtotime($workerProfile->birthDay['date']));
           // $date=\Morilog\Jalali\jDateTime::convertNumbers($date);
            $data['date']=$date;
           // dd($workerProfile->status);
            if ($workerProfile->status=='pending')
                $workerProfileStatus='منتظر تایید';
            elseif ($workerProfile->status=='reject')
                $workerProfileStatus ='رد شده';
            else
                $workerProfileStatus='تایید شده';
            $data['workerProfileStatus']=$workerProfileStatus;

        }

        $data['user']=$user;
        $data['workerProfile']=$workerProfile;

        $workerProfileStatus='pending';

        return view('admin.pages.user.editUser')->with($data);
    }

    function editUser(Request $request)
    {

        $this->validate($request,[
            'nameUser' => 'required',
            'familyUser' => 'required',
            'mobileUser' => 'required',
        ]);

        $userBynumber=User::where('phone_number',$request['mobileUser'])->first();
        if ($userBynumber){
            if (!($userBynumber->id==$request['idUser'])){
                $message['error']='کاربری با این شماره تلفن وجود دارد';
                return redirect()->back()->with($message);
            }
        }



        $user = User::find($request['idUser']);
        $user->name=$request['nameUser'];
        $user->family=$request['familyUser'];
        $user->phone_number=$request['mobileUser'];
        $user->email=$request['emailUser'];
        $user->password =bcrypt($request['phone_number']);
        $user->status= $request['status'];
        if ($user->save())
            $message['success']='یوزر با موفقیت ویرایش شد';
        else $message['error']='مجددا تلاش کنید';
        return redirect()->back()->with($message);
    }
    function editWorkerProfile(Request $request)
    {
        //dd($request['imageProfile']);
        $this->validate($request,[
            'nationCodeProfile' => 'required',
            'phoneProfile' => 'required',
            'addressProfile' => 'required',
            'birthdayProfile' => 'required',
            'imageProfile'   => 'image|mimes:jpeg,png,jpg|max:512'
        ]);




        if (!($this->CheckNationalCode($request['nationCodeProfile'])))
        {

            $message['error']='کد ملی وارد شده صحیح نمیباشذ';
            return redirect()->back()->with($message);
        }




        $workerProfile = WorkerProfile::where('user_id',$request['idUser'])->first();
        $workerProfileByNationalCode = WorkerProfile::where('nationalCode',$request['nationCodeProfile'])->first();
        if ($workerProfileByNationalCode)
        {
            if (!($workerProfileByNationalCode->id=$workerProfile->id))
            {
                $message['error']='کد ملی وارد شده اختصاص به کاربری دیگر دارد';
                return redirect()->back()->with($message);
            }
        }

       if ($request['imageProfile'])
       {
           $imageName = $workerProfile->id.'.'.request()->imageProfile->getClientOriginalExtension();

           if (file_exists((public_path('images/workers').'/'.$workerProfile->id).'.png'))
               unlink((public_path('images/workers').'/'.$workerProfile->id).'.png');
           if (file_exists((public_path('images/workers').'/'.$workerProfile->id).'.jpg'))
               unlink((public_path('images/workers').'/'.$workerProfile->id).'.jpg');
           if (file_exists((public_path('images/workers').'/'.$workerProfile->id).'.png'))
               unlink((public_path('images/workers').'/'.$workerProfile->id).'.png');

           request()->imageProfile->move(public_path('images/workers'), $imageName);
           $path=(public_path('images/workers').'/'.$imageName);
           $file =fopen($path, 'r');
       }


        $workerProfile->nationalCode        =$request['nationCodeProfile'];
        $workerProfile->address             =$request['addressProfile'];
        $workerProfile->home_phone_number   =$request['phoneProfile'];
        $workerProfile->field               =$request['field'];
        $workerProfile->last_education      =$request['lastEducationProfile'];
        $workerProfile->gender              =$request['gender'];
        $workerProfile->status              =$request['statusProfile'];
        $workerProfile->marriage_status     =$request['marriage_status'];
        $workerProfile->another_capability  =$request['anotherCapabilityProfile'];
        $workerProfile->certificates        =$request['certificatesProfile'];
        $workerProfile->experience          =$request['experienceProfile'];
        $workerProfile->birthDay            =\Morilog\Jalali\jDateTime::createDatetimeFromFormat('Y-m-d H:i:s', $request['birthdayProfile'].' 00:00:00');

        if ($workerProfile->save())
        {
            $message['success']='پروفایل کاربر به درستی ویرایش شد';
        }else
            $message['error']='مجددا تلاش کنید';

        return redirect()->back()->with($message);






    }

    function checkNationalCode($code='')
    {
        $code = (string) preg_replace('/[^0-9]/','',$code);
        if(strlen($code)>10 or strlen($code)<8)
            return false;

        if(strlen($code)==8)
            $code = "00".$code;

        if(strlen($code)==9)
            $code = "0".$code;

        $list_code = str_split($code);
        $last = (int) $list_code[9];
        unset($list_code[9]);
        $i = 10;
        $sum = 0;
        foreach($list_code as $key=>$_)
        {

            $sum += intval($_) * $i;
            $i--;
        }

        $mod =(int) $sum % 11;

        if($mod >= 2)
            $mod = 11 - $mod;

        if( $mod != $last)
            return false;

        for($i=0;$i<10;$i++)
        {
            $str = str_repeat($i,10);
            if($str==$code)
                return false;
        }

        return true;
    }

}
