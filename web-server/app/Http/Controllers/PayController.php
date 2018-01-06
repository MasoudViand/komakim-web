<?php

namespace App\Http\Controllers;

use App\OrderPayment;
use App\Transaction;
use App\Wallet;
use Illuminate\Http\Request;
use Mockery\Exception;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;

class PayController extends Controller
{
    function index($order_id,$amount)
    {



        $data['order_id']=$order_id;
        $data['amount']=$amount;

        return view('payment.pay')->with($data);
    }

    function callback(Request $request)
    {

        $data =null;
        if( ! isset($_POST['State']) or $_POST['State']!='OK')
        {

            $data['error']='پرداخت ناموفق';
            return view('payment.callback')->with($data);
        }


        $order_id =  $_POST['ResNum'];

        $orderPeyment =OrderPayment::find($order_id);

        if(empty($orderPeyment))
        {

            $data['error']='چنین تراکنشی موجود نیست';
            return view('payment.callback')->with($data);

        }

        if($orderPeyment->ip != $_SERVER['REMOTE_ADDR'])
        {

            $data['error']='آی پی پرداخت کننده مطابقت ندارد';
            return view('payment.callback')->with($data);

        }


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

            $mid  = 123456; // شماره مشتری بانک سامان
            $pass = 11111; // پسورد بانک سامان
            $result        = $soapProxy->VerifyTransaction($ref_num,$mid);

        }
        catch(Exception $e)
        {
            $data['error']='خطا در اتصال به وبسرویس ';
            return view('payment.callback')->with($data);

        }

        if($result != ($orderPeyment->amount))
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

        if($result == ($orderPeyment->amount))
        {
            // تراکنش موفق و ثبت شماره رسید دیجیتال

            $orderPeyment->status='success';
            $orderPeyment->ref_num =$ref_num;

            if ($orderPeyment->save())
            {

                $transAction = new \stdClass();

                $transAction->amount=$orderPeyment->amount;

                $transAction->created_at = new UTCDateTime(time()*1000);

                $transAction->user_id =new ObjectID($request->user()->id);

                $transAction->type =Transaction::CHARGE_FROM_BANK;
                $meta = new \stdClass();
                $meta->type= Transaction::CHARGE_FROM_BANK;
                $meta->ref_num =$ref_num;
                $meta->result = $result;

                $transAction->meta =$meta;

                $model = Transaction::raw()->insertOne($transAction);

                $wallet = Wallet::where('user_id',$request->user()->id)->first();
                if (!$wallet)
                {
                    $wallet = new \stdClass();

                    $wallet->user_id = $request->user()->id;
                    $wallet->amount = $orderPeyment->amount;
                    $wallet->updated_at =new UTCDateTime(time()*1000);
                    $model = Wallet::raw()->insertOne($wallet);


                }else
                {
                    $wallet->amount =$wallet->amount +$orderPeyment->amount;
                    $wallet->updated_at =new UTCDateTime(time()*1000);
                    $wallet->save();
                }


                $data['order_id']=$order_id;
                $data['ref_num']=$ref_num;
                return view('payment.callback')->with($data);

            }

        }


    }
}