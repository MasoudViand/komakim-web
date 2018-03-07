<?php

namespace App\Http\Controllers;

use App\Category;
use App\OrderPayment;
use App\RepeatQuestion;
use App\Setting;
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


        if (!$this->checkNationalCode($request->input('nationalCode')))
        {
            $message['error'] = 'کد ملی اشتتباه است';
            return redirect()->back()->with($message);
        }





        $user = User::where('phone_number',$request['mobileNumber'])->first();


        if ($user){
            $message['error'] = 'این شماره تلفن قبلا ثبت شده است ';
            return redirect()->back()->with($message);
        }

        $user =new User();
        $user->name =$request['name'];
        $user->family =$request['family'];
        $user->email =$request['email'];
        $user->status = User::ENABLE_USER_STATUS;
        $user->password=bcrypt($request['mobileNumber']);
        $user->phone_number=$request['mobileNumber'];
        $user->isCompleted =true;
        $user->role =User::WORKER_ROLE;
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



    function notif()
    {
        function sendMessage(){
            $content = array(
                "en" => 'English Message'
            );

//            $fields = array(
//                'app_id' => "5cf31f6e-0526-4083-841b-03d789183ab8",
//                'included_segments' => array('All'),
//                'data' => array("foo" => "bar"),
//                'contents' => $content
//            );
            $fields = array(
                'app_id' => "5cf31f6e-0526-4083-841b-03d789183ab8",
                'include_player_ids' => array("bb2d3340-00eb-4b09-9ed4-5250156214af"),
                'data' => array("foo" => "bar"),
                'contents' => $content
            );

            $fields = json_encode($fields);
            print("\nJSON sent:\n");
            print($fields);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                'Authorization: Basic Yzc0M2E3NzItYjZmMS00MDg4LWJiZDAtMjZkZWI4NDJmNDhi'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            $response = curl_exec($ch);
            curl_close($ch);

            return $response;
        }

        $response = sendMessage();
        $return["allresponses"] = $response;
        $return = json_encode( $return);

        print("\n\nJSON received:\n");
        print($return);
        print("\n");
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

    function showRepeadQuestions()
    {

        $repeadQuestions=RepeatQuestion::all();

        $data['repeadQuestions']=$repeadQuestions;


        return view('client.repeat_questions')->with($data);




    }
    function showrules()
    {
        $rules =Setting::where('type','rules')->first();


        $data['rules']=$rules;

        return view('client.rules')->with($data);
    }

    function showWorkWithUsCondition()
    {

        $workWithUsCondition =Setting::where('type','workWithUsCondition')->first();


        $data['workWithUsCondition']=$workWithUsCondition;

        return view('client.work_with_us_condition')->with($data);

    }

}
