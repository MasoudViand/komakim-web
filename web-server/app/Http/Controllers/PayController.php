<?php

namespace App\Http\Controllers;

use App\OrderPayment;
use App\Transaction;
use App\User;
use App\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Larabookir\Gateway\Gateway;
use Mockery\Exception;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;
use GuzzleHttp;


class PayController extends Controller
{
    function index($phone_number,$amount)
    {


        $user = User::where('phone_number',$phone_number)->first();
        if (!$user)
        {
            $data['error']='شماره تلفن اشتباه است یا در سامانه ثبت نشده است';
            return view('payment.saman-redirector')->with($data);

        }
        if (((int)$amount)<=0 )
        {
            $data['error']='مقدار یاید عدد و مثبت باشد';
            return view('payment.saman-redirector')->with($data);

        }




        $payOrder = new \stdClass();

        $payOrder->amount=(int)$amount;
        $payOrder->ip=$_SERVER['REMOTE_ADDR'];

        $payOrder->created_at = new UTCDateTime(time()*1000);

        $payOrder->user_id =new ObjectID($user->id);


        $model = OrderPayment::raw()->insertOne($payOrder);

        $orderPayment=OrderPayment::find((string)($model->getInsertedId()));
        $amount=$amount*10;




        $data['order_id']=(string)$orderPayment->id;
        $data['amount']=$amount;

        return view('payment.saman-redirector')->with($data);
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



           // $data['error']='پرداخت ناموفق';
            return view('payment.callback')->with($data);
        }


        $order_id =  $_POST['ResNum'];

        $orderPeyment =OrderPayment::find($order_id);

        if(empty($orderPeyment))
        {

            $data['error']='چنین تراکنشی موجود نیست';
            return view('payment.callback')->with($data);

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
            return view('payment.callback')->with($data);
        }
        if( ! isset($_POST['RefNum']))
        {
            $data['error']='رسید دیجیتال ست نشده است';
            return view('payment.callback')->with($data);
        }
        $ref_num = $_POST['RefNum'];

        $check = OrderPayment::where('ref_num',$ref_num)->first();


        if( ! empty($check))
        {

            $data['error']='رسید دیجیتال قبلا ثبت شده است ';
            return view('payment.callback')->with($data);

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
            return view('payment.callback')->with($data);

        }

        if($result != ($orderPeyment->amount*10))
        {
            // مغایرت مبلغ پرداختی

            if($result<0)
            {
                $data['error']="کد خطای بانک سامان $result ";
                return view('payment.callback')->with($data);

            }

            // مغایرت و برگشت دادن وجه به حساب مشتری
            if($result>0)
            {
                $data['error']="شما باید مبلغ {$orderPeyment->amount} ریال را پرداخت میکردید در صورتیکه مبلغ {$result}ریال را پرداخت کردید ! مبلغ شما به حسابتان برگشت داده شد آخرین بارتان باشد !!!";
                $soapProxy->ReverseTransaction($ref_num,$mid,$pass,$result);

                return view('payment.callback')->with($data);
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
                return view('payment.callback')->with($data);

            }

        }

    }
}
