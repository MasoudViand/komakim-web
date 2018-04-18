<?php

namespace App\Http\Controllers\Api;

use App\DiscountCode;
use App\DiscountCodeLog;
use App\FinancialReport;
use App\Jobs\RegisterStatusOrderRevisionJob;
use App\Jobs\SendNotificationToSingleUserJobWithFcm;
use App\Jobs\SendSmsToSingleUser;
use App\Order;
use App\OrderPayment;
use App\OrderStatusRevision;
use App\Service;
use App\Setting;
use App\Transaction;
use App\User;
use App\Wallet;
use Faker\Provider\DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;

class WalletController extends Controller
{
    function charge(Request $request)
    {

        if (!$request->has('amount'))
            return response()->json(['errors'=>'amount is require'])->setStatusCode(417);

        $payOrder = new \stdClass();


        $payOrder->amount=$request->input('amount');
        $payOrder->ip=$_SERVER['REMOTE_ADDR'];

        $payOrder->created_at = new UTCDateTime(time()*1000);

        $payOrder->user_id =new ObjectID($request->user()->id);


        $model = OrderPayment::raw()->insertOne($payOrder);

        $orderPayment=OrderPayment::find((string)($model->getInsertedId()));

        return response()->json(['url'=>URL::to('/').'/pay/'.$orderPayment->id.'/'.$orderPayment->amount,]);
    }


    function payOrder(Request $request)
    {

        if (!$request->has('order_id'))
            return response()->json(['errors'=>'order_id is require'])->setStatusCode(417);


        $order = Order::find($request->input('order_id'));

        if (!$order)
            return response()->json(['errors'=>'سفارشی پیدا نشد '])->setStatusCode(417);

        if ($order['user_id'] and (string)$order['user_id']!=$request->user()->id)
            return response()->json(['errors'=>'این سفارش به شما تعلق ندارد'])->setStatusCode(420);


        if (!($order['status']==OrderStatusRevision::FINISH_ORDER_BY_WORKER_STATUS))
            return response()->json(['errors'=>'وضعیت سفارش انجام شده توسط کاربر نیست'])->setStatusCode(420);


        $wallet = Wallet::where('user_id',new ObjectID($request->user()->id))->first();



        $total_price=$order->total_price;




        if ($order->discount)
        {


            $total_price =$total_price-$order->discount;
            $prevDisCountLog = DiscountCodeLog::where('discount_code_id',new ObjectID($order->discount_code_id))->orderby('_id','desc')->first();

            if (!$prevDisCountLog)
                $count =1;
            else
                $count=$prevDisCountLog->count+1;
            $discountCodeLog= new \stdClass();

            $discountCodeLog->user_id = new ObjectID($request->user()->id);
            $discountCodeLog->order_id = new ObjectID($order->id);
            $discountCodeLog->discount_code_id = $order->discount_code_id;
            $discountCodeLog->count = $count;

            $model= DiscountCodeLog::raw()->insertOne($discountCodeLog);

        }


        if (!$wallet or $wallet->amount<$total_price)
        {


            return response()->json(['errors'=>'مقدار کیف پول کمتر از قیمت سفارش است '])->setStatusCode(417);
        }






        $wallet->amount =$wallet->amount -$total_price;
        $wallet->updated_at =new UTCDateTime(time()*1000);

        //Todo implement transactional pattern


        $wallet->save();





        $clientTransaction = new \stdClass();

        $clientTransaction->amount= $total_price;
        $clientTransaction->user_id =$request->user()->id;
        $clientTransaction->created_at =new UTCDateTime(time()*1000);
        $clientTransaction->type = Transaction::PAY_ORDER;
        $meta = new \stdClass();
        $meta->type= Transaction::PAY_ORDER;
        $meta->order_id =$order->id;
        $clientTransaction->meta =$meta;

        $model = Transaction::raw()->insertOne($clientTransaction);

        $workerWallet = Wallet::where('user_id',$order->worker_id)->first();

        $fanantialReport= $this->_calculateCommission($order);


        if (!$workerWallet)
        {
            $workerWallet = new \stdClass();
            $workerWallet->user_id =$order->worker_id;
            $workerWallet->amount = $fanantialReport->total_price -$fanantialReport->commission;
            $workerWallet->updated_at =new UTCDateTime(time()*1000);
            $model = Wallet::raw()->insertOne($workerWallet);
        }else
            {

                $workerWallet->amount =$wallet->amount +$fanantialReport->total_price -$fanantialReport->commission;
                $workerWallet->updated_at =new UTCDateTime(time()*1000);
                $workerWallet->save();
        }
        $workerTransaction = new \stdClass();

        $workerTransaction->amount= $fanantialReport->total_price -$fanantialReport->commission;
        $workerTransaction->user_id =$order->worker_id;
        $workerTransaction->created_at =new UTCDateTime(time()*1000);
        $workerTransaction->type = Transaction::DONE_ORDER;
        $meta = new \stdClass();
        $meta->type= Transaction::DONE_ORDER;
        $meta->order_id =$order->id;
        $workerTransaction->meta =$meta;

        $model = Transaction::raw()->insertOne($workerTransaction);

        $order->status=OrderStatusRevision::PAID_ORDER_BY_CLIENT_STATUS;
        $order->save();

        $user_id=$order->worker_id;

        $user =User::find($user_id);
        $phone_number=$user->phone_number;
        $message = 'سفارش با کد پیگیری '.$order->tracking_number.' با موفقیت پرداخت شد. کمکیم';

        $this->dispatch(new SendSmsToSingleUser($phone_number,$message));
        $this->dispatch(new RegisterStatusOrderRevisionJob($order->id,OrderStatusRevision::PAID_ORDER_BY_CLIENT_STATUS,$request->user()));
        $this->dispatch(new SendNotificationToSingleUserJobWithFcm($order->worker_id,'سفارش توسط مشتری پرداخت شد','',$order,User::WORKER_ROLE));


        return response()->json(['wallet'=>$wallet,'order'=>$order]);

    }

    function validateDiscountCode(Request $request)
    {
        $order = Order::find($request->input('order_id'));

        if (!is_null($order->discount))
            return response()->json(['errors' => 'برای این سفارش قبلا کد تخفیفی اضافه شده'])->setStatusCode(417);




        $discountCode = DiscountCode::where('name', $request->input('discount_code'))->first();


        if (!$discountCode) {
            return response()->json(['errors' => 'مقدار کد تخفیف وارد شده صحیح نیست'])->setStatusCode(417);
        }
        if (!$discountCode->status) return response()->json(['errors' => 'مقدار کد تخفیف غیر فعال شده'])->setStatusCode(417);

        $countOfUsed = $discountCode->count_of_used;



        if (!$countOfUsed) $countOfUsed =0;


        if ($discountCode->total_use_limit != 'unlimited' and $countOfUsed >= $discountCode->total_use_limit) return response()->json(['errors' => 'مهلت استفاده از کد تخفیف پایان یافته است'])->setStatusCode(417);


        if ($discountCode->expired_at != 'unlimited' and $discountCode->expired_at->todateTime() < new \DateTime()) return response()->json(['errors' => 'مهلت استفاده از کد تخفیف پایان یافته است'])->setStatusCode(417);

        if ($discountCode->fields != 'unlimited')
        {

            $fields =$discountCode->fields;
            if (is_bool(array_search($order->category_id,$fields)))
            {
                if(! array_search($order->category_id,$fields))
                    return response()->json(['errors'=>'کد تخفیف وارد شده اشتباه است'])->setStatusCode(417);
            }

        }

        if ($discountCode->user_limit != 'unlimited')
        {
            $usedTimeByUser = DiscountCodeLog::where('user_id',new ObjectID($request->user()->id))->count();
            if ( $usedTimeByUser>=$discountCode->user_limit)
                return response()->json(['errors'=>'کد تخفیف وارد شده بیش از حد مجاز توسط کاربر استفاده شده است'])->setStatusCode(417);
        }


        if ($order->discount_code_id and $order->discount_code_id == $discountCode->id)
        {
            return response()->json(['errors' => 'این کد تخفیف قبلا برای این سفارش استفاده شده '])->setStatusCode(417);
        }



        if ($discountCode->type==DiscountCode::PERCENT_TYPE)
        {

            $discount = ($order->total_price*$discountCode->value)/100;
        }else
        {
            $discount = $discountCode->value;
        }

        if ($discountCode->upper_limit_use != 'unlimited' and $discount>=$discountCode->upper_limit_use)
            $discount=$discountCode->upper_limit_use;

        
        $order->discount = $discount;
        $discountCode->count_of_used =$countOfUsed+1;

        $discountCode->save();

        $order->discount_code_id = new ObjectID($discountCode->id);

        $order->save();
         return response()->json(['discount'=>$discount]);
    }

    private function _calculateCommission($order)
    {
        $services= $order->services;


        $fanantialReport =new FinancialReport();

        $total_price =0;
        $total_commission =0;
        $commissionConstModel = Setting::where('type','commission')->first();
        $commissionConstModel? $commissionConst=$commissionConstModel->value:$commissionConst=5000;

        foreach ($services as $item)
        {



            $serviceModel=Service::find($item['service_id']);

            if ($serviceModel)
            {
                $minimumNumber =(int)$serviceModel->minimum_number;


                $price         =(int)$serviceModel->price;

                $unit_count = $item['unit_count'];

                if ($unit_count<$minimumNumber)
                    $unit_count = $minimumNumber;

                $commission=$commissionConst;


                if ($serviceModel->commission)
                {
                    $commission=$serviceModel->commission;
                }



                $total_commission = $total_commission+ ((int)($unit_count/$minimumNumber))*$commission;


                $total_price=$total_price+ $unit_count*$price;
            }
            else
            {
                $unit_count = $item['unit_count'];
                $commission=$commissionConst;
                $total_commission = $total_commission+ ((int)($unit_count))*$commission;
                $total_price=$total_price+ $unit_count*$item['unit_price'];
            }


        }
        if($order->discount)
            $total_price = $total_price- $order->discount;
        $fanantialReport->total_price =$total_price;
        $fanantialReport->commission = $total_commission;
        $fanantialReport->created_at= new UTCDateTime(time()*1000);



        if ($fanantialReport->save())
            return $fanantialReport;
        else
            return false;
    }


}

