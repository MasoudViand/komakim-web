<?php

namespace App\Http\Controllers\Api;

use App\CancelReason;
use App\Jobs\CheckServiceAccepted;
use App\Jobs\FindWorker;
use App\Jobs\RegisterStatusOrderRevisionJob;
use App\Jobs\SendNotificationToSingleUserJobWithFcm;
use App\Order;
use App\OrderStatusRevision;
use App\Review;
use App\User;
use App\WorkerProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;
use Mockery\Tests\React_WritableStreamInterface;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;
use function PHPSTORM_META\type;


class OrderController extends Controller
{


        function listActiveOrder(Request $request)

        {

            $activeStatus=[OrderStatusRevision::WAITING_FOR_WORKER_STATUS,OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS,OrderStatusRevision::START_ORDER_BY_WORKER_STATUS,OrderStatusRevision::FINISH_ORDER_BY_WORKER_STATUS,OrderStatusRevision::EDIT_BY_WORKER_STATUS];

            $orders = Order::whereIn('status',$activeStatus)->where('user_id',new ObjectID($request->user()->id))->get();

            foreach ($orders as $item)
            {

                if ($item->worker_id)
                    $item->worker=User::find($item->worker_id);
                else
                    $item->worker=false;
                if ($item->revisions)
                {
                    $item->services=$item->revisions[0]['services'];
                    unset($item->revisions);
                }

            }
            return response()->json(['orders' => $orders]);
        }

        function listArchiveOrder(Request $request)
        {
            $offset=0;
            $limit=30;
            if ($request->has('offset'))
                $offset=$request->input('offset');
            if ($request->has('limit'))
                $limit = $request->input('limit');

            $archiveStatus = [OrderStatusRevision::PAID_ORDER_BY_CLIENT_STATUS,OrderStatusRevision::CANCEL_ORDER_BY_CLIENT_STATUS,OrderStatusRevision::CANCEL_ORDER_BY_WORKER_STATUS];

            $archiveOrders = Order::whereIn('status',$archiveStatus)->where('user_id',new ObjectID($request->user()->id))->skip($offset)->take($limit)->get();

            $activeStatus=[OrderStatusRevision::WAITING_FOR_WORKER_STATUS,OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS,OrderStatusRevision::START_ORDER_BY_WORKER_STATUS,OrderStatusRevision::FINISH_ORDER_BY_WORKER_STATUS,OrderStatusRevision::EDIT_BY_WORKER_STATUS];

            $activeOrders = Order::whereIn('status',$activeStatus)->where('user_id',new ObjectID($request->user()->id))->get();


            $orders = array_merge($activeOrders->toArray(),$archiveOrders->toArray());


            $ordersArray=[];

            foreach ($orders as $item)
            {


                if (key_exists('worker_id',$item))
                {


                    $item['worker']=User::find($item['worker_id']);


                    $url_image =  '/images/workers/profile-default-male.png';
                    $worker_profile  =WorkerProfile::where('user_id',new ObjectID($item['worker_id']))->first();
//
                    if (file_exists((public_path('images/workers') . '/' . $worker_profile->id) . '.png')) $url_image = ('/images/workers') . '/' .$worker_profile-id. '.png';
                    if (file_exists((public_path('images/workers') . '/' .$worker_profile->id) . '.jpg')) $url_image = ('/images/workers') . '/' .$worker_profile->id . '.jpg';
                    if (file_exists((public_path('images/workers') . '/' .$worker_profile->id) . '.jpeg')) $url_image = ('/images/workers') . '/' . $worker_profile->id . '.jpeg';
//
                    $url_image=URL::to('/').$url_image;
//
                    $item['worker']['url_image'] = $url_image;



                    $mah = (int)\Morilog\Jalali\jDateTime::strftime('m', $item['created_at']);
                    $rooz = (int)\Morilog\Jalali\jDateTime::strftime('j', $item['created_at']);
                    $sal = (int)\Morilog\Jalali\jDateTime::strftime('  y', $item['created_at']);
                    $persian_date =[
                        'day' =>$rooz,
                        'month' =>$mah,
                        'year'=>$sal
                    ];
                    $item['persian_date'] = $persian_date;



                    $review =Review::where('order_id',new ObjectID($item['_id']))->first();
                    if ($review)
                        $item['score'] = $review->score;



                }
                else
                    $item['worker']=null;

                unset($item->revisions);


                array_push($ordersArray,$item);



            }



            return response()->json(['orders'=>$ordersArray]);

        }

        function editOrder(Request $request)
        {

            $order = $request->getContent();


            $order =(json_decode($order));

            if(!property_exists($order, 'services') || !is_array($order->services))
                return response()->json(['errors'=>'services is invalid'])->setStatusCode('417');

            if(!property_exists($order, 'total_price'))
                return response()->json(['errors'=>'total_price is invalid'])->setStatusCode('417');

            if(!property_exists($order, 'parent_id'))
                return response()->json(['errors'=>'parent_id is invalid'])->setStatusCode('417');

            if (count($order->services)<1)
            {
                return response()->json(['errors'=>'سرویسی برای ویرایش وجود ندارد'])->setStatusCode('417');

            }


            $orderModel=Order::find($order->parent_id);


            if (!$orderModel or $orderModel->status!=OrderStatusRevision::START_ORDER_BY_WORKER_STATUS)
                return response()->json(['errors'=>'سفارش وجود ندارد یا وضعیت  شروع به کار ندارد'])->setStatusCode('417');

            $revisions =$orderModel->revisions;

            $revision['services']=$order->services;
            $revision['total_price']=$order->total_price;
            $revision['created_at']=new UTCDateTime(time()*1000);
            $revision['tracking_number']=$orderModel->tracking_number.'.'.(count($orderModel->revisions));
            $revision['status']=Order::PENDING_SERVICE_STATUS;
            array_unshift($revisions,$revision);
            $orderModel->revisions=$revisions;


            $orderModel->status=OrderStatusRevision::EDIT_BY_WORKER_STATUS;



           if ($orderModel->save())
            {


                $this->dispatch(new RegisterStatusOrderRevisionJob($orderModel->id,OrderStatusRevision::EDIT_BY_WORKER_STATUS,$request->user()));
                $this->dispatch(new SendNotificationToSingleUserJobWithFcm($orderModel->user_id,'ویرایش کار توسط خدمه','', $orderModel,User::CLIENT_ROLE));


                return response()->json(['order'=>$orderModel]);
            }
            else
                return response()->json(['errors'=>'internal server error']);


        }
        function approveEditOrder(Request $request)
        {
            if (!$request->has('order_id')){
                return response()->json(['errors'=>'order id is require'])->setStatusCode(417);
            }
            $order = Order::find($request->input('order_id'));
            if ($order['user_id'] and (string)$order['user_id']!=$request->user()->id)
                return response()->json(['errors'=>'این سفارش به شما تعلق ندارد'])->setStatusCode(420);


            if (!($order['status']==OrderStatusRevision::EDIT_BY_WORKER_STATUS))
                return response()->json(['errors'=>'وضعیت سفارش ویرایش شده توسط خدمه نیست'])->setStatusCode(420);


            $order->status =OrderStatusRevision::START_ORDER_BY_WORKER_STATUS;
            $order->total_price=$order->revisions[0]['total_price'];
            $order->services=$order->revisions[0]['services'];
            $revisions = $order->revisions;
            $revision=$revisions[0];
            $revision['status']=Order::ACCEPTED_SERVICE_STATUS;
            $revisions[0]=$revision;
            $order->revisions=$revisions;

            if ($order->save())
            {
                $this->dispatch(new RegisterStatusOrderRevisionJob($order->id,OrderStatusRevision::APPROVE_EDIT_BY_CLIENT_STATUS,$request->user()) );
                $this->dispatch(new SendNotificationToSingleUserJobWithFcm($order->worker_id,'تایید ادامه کار','',$order,User::WORKER_ROLE));

                return response()->json(['order'=>$order]);

            }else
            {
                return response()->json(['errors'=>'internal server errors'])->setStatusCode(500);
            }
        }

    function deniedEditOrder(Request $request)
    {
        if (!$request->has('order_id')){
            return response()->json(['errors'=>'order id is require'])->setStatusCode(417);
        }
        $order = Order::find($request->input('order_id'));
        if ($order['user_id'] and (string)$order['user_id']!=$request->user()->id)
            return response()->json(['errors'=>'این سفارش به شما تعلق ندارد'])->setStatusCode(420);


        if (!($order['status']==OrderStatusRevision::EDIT_BY_WORKER_STATUS))
            return response()->json(['errors'=>'وضعیت سفارش ویرایش شده توسط خدمه نیست'])->setStatusCode(420);


        $order->status =OrderStatusRevision::START_ORDER_BY_WORKER_STATUS;
        $revisions = $order->revisions;
        $revision=$revisions[0];
        $revision['status']=Order::REJECTED_SERVICE_STATUS;
        $revisions[0]=$revision;
        $order->revisions=$revisions;

        if ($order->save())
        {
            $this->dispatch(new RegisterStatusOrderRevisionJob($order->id,OrderStatusRevision::DENIED_EDIT_BY_CLIENT_STATUS,$request->user()) );
            $this->dispatch(new SendNotificationToSingleUserJobWithFcm($order->worker_id,'ویرایش انجام شده مورد تایید قرار نگرفت','',$order,User::WORKER_ROLE));
            return response()->json(['order'=>$order]);

        }else
        {
            return response()->json(['errors'=>'internal server errors'])->setStatusCode(500);
        }
    }




    function listActiveOrderWorker(Request $request)

    {

        $activeStatus=[OrderStatusRevision::WAITING_FOR_WORKER_STATUS,OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS,OrderStatusRevision::START_ORDER_BY_WORKER_STATUS,OrderStatusRevision::EDIT_BY_WORKER_STATUS];

        $order = Order::whereIn('status',$activeStatus)->where('worker_id',new ObjectID($request->user()->id))->first();

        if (!$order)
            return response()->json(['order' => $order]);



            if ($order->worker_id)
                $order->worker=User::find($order->worker_id);
            else
                $order->worker=null;
            if ($order->user_id)
                $order->client = User::find($order->user_id);
            if ($order->revisions)
            {
                $order->services=$order->revisions[0]['services'];
                unset($order->revisions);


        }
        return response()->json(['order' => $order]);
    }

        function listArchiveOrderWorker(Request $request)
    {
        $offset=0;
        $limit=30;
        if ($request->has('offset'))
            $offset=$request->input('offset');
        if ($request->has('limit'))
            $limit = $request->input('limit');

        $archiveStatus = [OrderStatusRevision::PAID_ORDER_BY_CLIENT_STATUS,OrderStatusRevision::CANCEL_ORDER_BY_CLIENT_STATUS,OrderStatusRevision::CANCEL_ORDER_BY_WORKER_STATUS,OrderStatusRevision::FINISH_ORDER_BY_WORKER_STATUS];

        $orders = Order::whereIn('status',$archiveStatus)->where('worker_id',new ObjectID($request->user()->id))->skip($offset)->take($limit)->get();

        foreach ($orders as $item)
        {

            $item->worker=User::find($item->worker_id);

            unset($item->revisions);
            $mah = (int)\Morilog\Jalali\jDateTime::strftime('m', $item->created_at);
            $rooz = (int)\Morilog\Jalali\jDateTime::strftime('j', $item->created_at);
            $sal = (int)\Morilog\Jalali\jDateTime::strftime('  y', $item->created_at);
            $persian_date =[
                'day' =>$rooz,
                'month' =>$mah,
                'year'=>$sal
            ];
            $item->persian_date = $persian_date;



            $review =Review::where('order_id',new ObjectID($item->id))->first();
            if ($review)
                $item->score = $review->score;
            else
                $item->score = null;
        }

        return response()->json(['orders'=>$orders]);

    }



    function cancelOrder(Request $request)
    {


        if (!$request->has('order_id'))
            return response()->json(['errors','order_id is require'])->setStatusCode(417);


        $order = Order::find($request->input('order_id'));


        if ($order->status == OrderStatusRevision::WAITING_FOR_WORKER_STATUS or $order->status == OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS )
        {
            $user = $request->user();

            $workerId=$order->worker_id;



            if ($workerId)
            {
                $workerProfile = WorkerProfile::where('user_id',$workerId)->first();
                $workerProfile->has_active_order=false;
                $workerProfile->save();
            }



            if ($user->role ==User::WORKER_ROLE)
            {


                $this->dispatch(new RegisterStatusOrderRevisionJob($order->id,OrderStatusRevision::CANCEL_ORDER_BY_WORKER_STATUS,$user));

                if ( $order->status == OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS)
                    $this->dispatch(new SendNotificationToSingleUserJobWithFcm($order->user_id,'سفارش توسط خدمه لغو شد','',$order,User::CLIENT_ROLE));
                $order->status=OrderStatusRevision::CANCEL_ORDER_BY_WORKER_STATUS;


            }
            if ($user->role == User::CLIENT_ROLE)
            {

                $this->dispatch(new RegisterStatusOrderRevisionJob($order->id,OrderStatusRevision::CANCEL_ORDER_BY_CLIENT_STATUS,$user));
                if ( $order->status == OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS)
                $this->dispatch(new SendNotificationToSingleUserJobWithFcm($order->worker_id,'سفارش توسط مشتری لغو شد','',$order,User::WORKER_ROLE));
                $order->status=OrderStatusRevision::CANCEL_ORDER_BY_CLIENT_STATUS;


            }
            if ($request->has('cancel_reason'))
                $order->cancel_reason=$request->input('cancel_reason');
            if ($order->save())
            {
                return response()->json(['order'=>$order]);
            }else
                return response()->json(['errors'=>'internal server errors'])->setStatusCode(500);
        }
        else
            return response()->json(['errors'=>'این سفارش دیگر قابل حذف کردن نیست لطفا در صورت بروز مشکل با پشتیبانی تماس حاصل فرمایید '])->setStatusCode(420);

    }

    function receiveCancelReason(Request $request)
    {
        
        $role =$request->user()->role;


        $cancelReasons =CancelReason::where('type',$role)->get();

        return response()->json(['cancelReasons'=>$cancelReasons]);

    }

}

