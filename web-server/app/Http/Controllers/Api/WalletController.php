<?php

namespace App\Http\Controllers\Api;

use App\Jobs\RegisterStatusOrderRevisionJob;
use App\Order;
use App\OrderPayment;
use App\OrderStatusRevision;
use App\Transaction;
use App\Wallet;
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
            return response()->json(['error'=>'amount is require'])->setStatusCode(417);

        $payOrder = new \stdClass();

        $payOrder->amount=$request->input('amount');
        $payOrder->ip=request()->ip();

        $payOrder->created_at = new UTCDateTime(time()*1000);

        $payOrder->user_id =new ObjectID($request->user()->id);


        $model = OrderPayment::raw()->insertOne($payOrder);

        $orderPayment=OrderPayment::find((string)($model->getInsertedId()));

        return response()->json(['url'=>URL::to('/').'/pay/'.$orderPayment->id.'/'.$orderPayment->amount,]);
    }


    function payOrder(Request $request)
    {
        if (!$request->has('order_id'))
            return response()->json(['error'=>'order_id is require'])->setStatusCode(417);


        $order = Order::find($request->input('order_id'));

        if (!$order)
            return response()->json(['error'=>'سفارشی پیدا نشد '])->setStatusCode(417);

        $wallet = Wallet::where('user_id',$request->user()->id)->first();

        $total_price=$order->total_price;

        if ($order->revisions)
        {
            $total_price=$order->revisions[0]['total_price'];
            unset($order->revisions);
        }

        if (!$wallet or $wallet->amount<$total_price)
        {
            return response()->json(['error'=>'مقدار کیف پول کمتر از قیمت سفارش است '])->setStatusCode(417);
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

        if (!$workerWallet)
        {
            $workerWallet = new \stdClass();

            $workerWallet->user_id =$order->worker_id;
            $workerWallet->amount = $total_price;
            $workerWallet->updated_at =new UTCDateTime(time()*1000);
            $model = Wallet::raw()->insertOne($workerWallet);
        }else
            {
                $workerWallet->amount =$wallet->amount +$total_price;
                $workerWallet->updated_at =new UTCDateTime(time()*1000);
                $workerWallet->save();
        }
        $workerTransaction = new \stdClass();

        $workerTransaction->amount= $total_price;
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

        $this->dispatch(new RegisterStatusOrderRevisionJob($order->id,OrderStatusRevision::PAID_ORDER_BY_CLIENT_STATUS,$request->user()));

        return response()->json(['wallet'=>$wallet,'order'=>$order]);

    }

}

