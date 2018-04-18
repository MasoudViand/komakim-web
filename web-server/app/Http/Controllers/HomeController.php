<?php

namespace App\Http\Controllers;

use App\Category;
use App\DiscountCode;
use App\Order;
use App\OrderPayment;
use App\OrderStatusRevision;
use App\RepeatQuestion;
use App\Setting;
use App\Transaction;
use App\User;
use App\Wallet;
use App\WorkerProfile;
use function GuzzleHttp\Psr7\_parse_message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Mockery\Exception;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;
use Validator;


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
        $data['header_title']='ثبت نام خدمه';



        return view('client.pages.work_with_us')->with($data);

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
        $workerProfile->status =WorkerProfile::WORKER_PENDING_STATUS;
        $workerProfile->availability_status =WorkerProfile::WORKER_UNAVAILABLE_STATUS;
        $workerProfile->has_active_order=false;
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

    function chargeAccount(Request $request)
    {


        $this->validate($request,[
            'mobile_number' => 'required|numeric||digits:11',
            'amount' => 'required|numeric|min:100',
        ]);

        $user = User::where('phone_number',$request->input('mobile_number'))->first();


        if (!$user)
            return redirect()->back()->with(['error'=>'شماره تلفن در سامانه ثبت نشده است']);

        $payOrder = new \stdClass();

        $payOrder->amount=(int)$request->input('amount');
        $payOrder->ip=$_SERVER['REMOTE_ADDR'];

        $payOrder->created_at = new UTCDateTime(time()*1000);

        $payOrder->user_id =new ObjectID($user->id);


        $model = OrderPayment::raw()->insertOne($payOrder);

        $orderPayment=OrderPayment::find((string)($model->getInsertedId()));
        $amount=$request->input('amount')*10;

        $data['order_id']=(string)$orderPayment->id;
        $data['amount']=$amount;

        return view('client.pages.saman-redirector')->with($data);

    }

    function callback(Request $request)
    {



        $data =null;
        if( ! isset($_POST['State']) or $_POST['State']!='OK')
        {
           switch ($_POST['State'])
           {
               case 'Canceled By User':
                   $data['error']='تراکنش توسط خریدار کنسل شد';
                   break;
               case 'Invalid Amount':
                   $data['error']='مبلغ سند برگشتی از مبلغ تراکنش اصلی بیشتر است';
                   break;
               case 'Invalid Transaction':
                   $data['error']='درخواست برگشت تراکنش رسیده است در حالی که تراکنش اصلی پیدا نمی شود';
                   break;
               case 'Invalid Card Number':
                   $data['error']='شماره کارت اشتباه است';
                   break;
               case 'No Such Issuer':
                   $data['error']='چنین صادر کننده کارتی وجود ندارد';
                   break;
               case 'Expired Card Pick Up':
                   $data['error']='از تاریخ انقضای کارت گذشته است و کارت دیگر معتبر نیست';
                   break;
               case 'Incorrect PIN':
                   $data['error']='رمز کارت (PIN) اشتباه وارد شده است';
                   break;
               case 'No Sufficient Funds':
                   $data['error']='موجودی به اندازه کافی در حساب شما نیست';
                   break;
               case 'Issuer Down Slm':
                   $data['error']='سیستم کارت بانک صادر کننده فعال نیست';
                   break;
               case 'TME Error':
                   $data['error']='خطا در شبکه بانکی';
                   break;
               case 'Exceeds Withdrawal Amount Limit':
                   $data['error']='مبلغ بیش از سقف برداشت است';
                   break;
               case 'Transaction Cannot Be Completed':
                   $data['error']='امکان سند خوردن وجود ندارد';
                   break;
               case 'Allowable PIN Tries Exceeded Pick Up':
                   $data['error']='رمز کارت (PIN) 3 مرتبه اشتباه وارد شده است در نتیجه کارت شما غیر فعال خواهد شد';
                   break;
               case 'Response Received Too Late':
                   $data['error']='تراکنش در شبکه بانکی Timeout خورده است';
                   break;
               case 'Suspected Fraud Pick Up':
                   $data['error']='فیلد CV2V و یا فیلد ExpDate اشتباه وارد شده و یا اصلا وارد نشده است';
                   break;

           }

            return redirect()->route('client.charge_account')->with($data);
        }


        $order_id =  $_POST['ResNum'];

        $orderPeyment =OrderPayment::find($order_id);

        if(empty($orderPeyment))
        {

            $data['error']='چنین تراکنشی موجود نیست';
            return redirect()->route('client.charge_account')->with($data);

        }

//        dd($_SERVER['REMOTE_ADDR']);
//
//        if($orderPeyment->ip != $_SERVER['REMOTE_ADDR'])
//        {
//
//            $data['error']='آی پی پرداخت کننده مطابقت ندارد';
//            return view('payment.callback')->with($data);
//
//        }


        if($orderPeyment->status=='success')
        {
            $data['error']='تراکنش قبلا وریفای شده است !';
            return redirect()->route('client.charge_account')->with($data);
        }
        if( ! isset($_POST['RefNum']))
        {
            $data['error']='رسید دیجیتال ست نشده است';
            return redirect()->route('client.charge_account')->with($data);
        }
        $ref_num = $_POST['RefNum'];

        $check = OrderPayment::where('ref_num',$ref_num)->first();


        if( ! empty($check))
        {

            $data['error']='رسید دیجیتال قبلا ثبت شده است ';
            return redirect()->route('client.charge_account')->with($data);

        }



        try
        {
            $soapclient = new \nusoap_client('https://sep.shaparak.ir/payments/referencepayment.asmx?WSDL','wsdl');
            $soapProxy    = $soapclient->getProxy() ;

            $mid  = 10917062; // شماره مشتری بانک سامان
            $pass = 7193628; // پسورد بانک سامان
            $result        = $soapProxy->VerifyTransaction($ref_num,$mid);


        }
        catch(Exception $e)
        {
            $data['error']='خطا در اتصال به وبسرویس ';
            return redirect()->route('client.charge_account')->with($data);

        }

        if($result != ($orderPeyment->amount*10))
        {
            // مغایرت مبلغ پرداختی

            if($result<0)
            {
                $data['error']="کد خطای بانک سامان $result ";
                return redirect()->route('client.charge_account')->with($data);

            }

            // مغایرت و برگشت دادن وجه به حساب مشتری
            if($result>0)
            {
                $data['error']="شما باید مبلغ {$orderPeyment->amount} ریال را پرداخت میکردید در صورتیکه مبلغ {$result}ریال را پرداخت کردید ! مبلغ شما به حسابتان برگشت داده شد آخرین بارتان باشد !!!";
                $soapProxy->ReverseTransaction($ref_num,$mid,$pass,$result);

                return redirect()->route('client.charge_account')->with($data);
            }
        }

        if($result == ($orderPeyment->amount)*10)
        {
            // تراکنش موفق و ثبت شماره رسید دیجیتال

            $orderPeyment->status='success';
            $orderPeyment->ref_num =$ref_num;

            if ($orderPeyment->save())
            {

                $transAction = new \stdClass();

                $transAction->amount=$orderPeyment->amount;

                $transAction->created_at = new UTCDateTime(time()*1000);

                $transAction->user_id =$orderPeyment->user_id;

                $transAction->type =Transaction::CHARGE_FROM_BANK;
                $meta = new \stdClass();
                $meta->type= Transaction::CHARGE_FROM_BANK;
                $meta->ref_num =$ref_num;
                $meta->result = $result;

                $transAction->meta =$meta;

                $model = Transaction::raw()->insertOne($transAction);

                $wallet = Wallet::where('user_id',$orderPeyment->user_id)->first();
                if (!$wallet)
                {
                    $wallet = new \stdClass();

                    $wallet->user_id = $orderPeyment->user_id;
                    $wallet->amount = (int)$orderPeyment->amount;
                    $wallet->updated_at =new UTCDateTime(time()*1000);
                    $model = Wallet::raw()->insertOne($wallet);


                }else
                {
                    $wallet->amount =$wallet->amount +$orderPeyment->amount;
                    $wallet->updated_at =new UTCDateTime(time()*1000);
                    $wallet->save();
                }

                $data['success']= 'کیف پول با موفقیت شارژ شد';
                $data['order_id']=$order_id;
                $data['ref_num']=$ref_num;
                return redirect()->route('client.charge_account')->with($data);

            }

        }

    }

    function sendEmail(Request $request)
    {

        $request = $request->getContent();


        $request =(json_decode($request));


        $validator = Validator::make((array)$request, [
            'email'     => 'email',
            'name'  => 'required',
            'mobile_number'  => 'required',
            'content'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()]);
        }



        $data['title']=$request->name;
        $data['mobile_number']=$request->mobile_number;
        $data['email']=null;
        if(key_exists( 'email',$request) )
            $data['email']=$request->email;

        $data['content'] = $request->content;


        Mail::send('emails.send', $data, function ($message) use($data)
        {

            if ($data['email'])
            {
                $message->subject('فرم تماس با ما کمکیم');
                $message->from([$data['email'] => $data['title']]);
            }


            $message->to('masoudviand@gmail.com');

        });

        return response()->json(['success'=>'ایمیل با موفقیت ارسال شد منتظر پاسخ باشید']);
    }




    function test()
    {

       $disounts =DiscountCode::all();

       foreach ($disounts as $disount)
       {
           $disount->total_use_limit='unlimited';
           $disount->fields='unlimited';
           $disount->upper_limit_use='unlimited';
           $disount->user_limit='unlimited';
           $disount->expired_at='unlimited';
           $disount->save();
       }
    }


}
