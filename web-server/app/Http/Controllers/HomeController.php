<?php

namespace App\Http\Controllers;

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
       return view('work_with_us');
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

    function callback(Request $request)
    {
        $data=[];

        if (!$request->has('State') or $request->input('State')!='OK')
            return view('callbak')->with(['error']);

        $order_id = (int) $request->input('ResNum');

        $orderPeyment = OrderPayment::find($order_id);


        if (!$orderPeyment)
            return view('callback')->message;


        if($orderPeyment->ip != $_SERVER['REMOTE_ADDR'])
        {
            echo "آی پی پرداخت کننده مطابقت ندارد";
            die;
        }

        if($data->status=='1')
        {
            echo "تراکنش قبلا وریفای شده است !";
            die;
        }

        if( ! isset($_POST['RefNum']))
        {
            echo "رسید دیجیتال ست نشده است";
            die;
        }

        $ref_num = $_POST['RefNum'];
        $check = [];
        if( ! empty($check))
        {
            echo "رسید دیجیتال قبلا ثبت شده است ";
            die;
        }

        try
        {
            $soapclient = new nusoap_client('https://sep.shaparak.ir/payments/referencepayment.asmx?WSDL','wsdl');
            $soapProxy    = $soapclient->getProxy() ;

            $mid  = 123456; // شماره مشتری بانک سامان
            $pass = 11111; // پسورد بانک سامان
            $result        = $soapProxy->VerifyTransaction($ref_num,$mid);

        }
        catch(Exception $e)
        {
            echo "خطا در اتصال به وبسرویس ";
            die;
        }

if($result != ($data->amount))
{
    // مغایرت مبلغ پرداختی

    if($result<0)
    {
        echo "کد خطای بانک سامان $result ";
        die;
    }

    // مغایرت و برگشت دادن وجه به حساب مشتری
    if($result>0)
    {
        echo "شما باید مبلغ {$data->amount} ریال را پرداخت میکردید در صورتیکه مبلغ {$result}ریال را پرداخت کردید ! مبلغ شما به حسابتان برگشت داده شد آخرین بارتان باشد !!!";
        $soapProxy->ReverseTransaction($ref_num,$mid,$pass,$result);
    }
}

if($result == ($data->amount))
{
    // تراکنش موفق و ثبت شماره رسید دیجیتال

    $aff = $db->query("UPDATE tbl SET ref_num=?,status=1 WHERE id=$order_id")->execute(array($ref_num));
    if( ! $aff)
        die('خطا در ثبت اطلاعات');

    echo "تراکنش با موفقیت انجام شد ، رسید دیجیتال $ref_num و شماره فاکتور $order_id";

}



    }

    protected function _saveProfile( $request ,$user_id)
    {
        $workerProfile= new WorkerProfile();
        $workerProfile->user_id=new ObjectID($user_id);
        $workerProfile->national_code=$request['nationalCode'];
        $workerProfile->address =$request['address'];
        $workerProfile->home_phone_number = $request['phoneNumber'];
        $workerProfile->birthDay =  \Morilog\Jalali\jDateTime::createDatetimeFromFormat('Y/m/d H:i:s', $request['birthday'].' 00:00:00');
        $workerProfile->field = $request['field'];
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
