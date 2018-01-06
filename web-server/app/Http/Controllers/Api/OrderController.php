<?php

namespace App\Http\Controllers\Api;

use App\Jobs\CheckServiceAccepted;
use App\Jobs\FindWorker;
use App\Jobs\RegisterStatusOrderRevisionJob;
use App\Order;
use App\OrderStatusRevision;
use App\Review;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mockery\Tests\React_WritableStreamInterface;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;


class OrderController extends Controller
{


        function listActiveOrder(Request $request)

        {
           // dd($request->user()->id);
            $activeStatus=[OrderStatusRevision::WAITING_FOR_WORKER_STATUS,OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS,OrderStatusRevision::START_ORDER_BY_WORKER_STATUS,OrderStatusRevision::FINISH_ORDER_BY_WORKER_STATUS,OrderStatusRevision::EDIT_BY_WORKER_STATUS];

            $orders = Order::whereIn('status',$activeStatus)->where('user_id',$request->user()->id)->get();

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

            $orders = Order::whereIn('status',$archiveStatus)->where('user_id',$request->user()->id)->skip($offset)->take($limit)->get();

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

            return response()->json(['orders'=>$orders]);

        }

        function editOrder(Request $request)
        {
            $order = $request->getContent();


            $order =(json_decode($order));
            if(!property_exists($order, 'services') || !is_array($order->services))
                return response()->json(['error'=>'services is invalid'])->setStatusCode('417');

            if(!property_exists($order, 'total_price'))
                return response()->json(['error'=>'total_price is invalid'])->setStatusCode('417');

            if(!property_exists($order, 'parent_id'))
                return response()->json(['error'=>'parent_id is invalid'])->setStatusCode('417');

           // dd($order->parent_id);

            $orderModel=Order::find($order->parent_id);

            if (!$orderModel->revisions)
                $revisions=[];
            else
                $revisions=$orderModel->revisions;

            $revision['services']=$order->services;
            $revision['total_price']=$order->total_price;
            $revision['created_at']=new UTCDateTime(time()*1000);
            $revision['tracking_number']=$orderModel->tracking_number.'.'.(count($revisions)+1);
            array_unshift($revisions,$revision);


            $orderModel->revisions=$revisions;
            $orderModel->status=OrderStatusRevision::EDIT_BY_WORKER_STATUS;

            if ($orderModel->save())
            {
                $this->dispatch(new RegisterStatusOrderRevisionJob($orderModel->id,OrderStatusRevision::EDIT_BY_WORKER_STATUS,$request->user()));

                return response()->json(['order'=>$order]);
            }
            else
                return response()->json(['error'=>'internall server error']);







        }

        function detailOrder(Request $request)
        {
            $order = Order::find($request->input('order_id'));

            $review = Review::where('order_id',new ObjectID($request->input('order_id')))->first();


            if ($review)
                $order->review=$review;
            else
                $order->review=false;


            return response()->json(['order'=>$order]);

        }






        ///////


        function listActiveOrderWorker(Request $request)

    {
        $activeStatus=[OrderStatusRevision::WAITING_FOR_WORKER_STATUS,OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS,OrderStatusRevision::START_ORDER_BY_WORKER_STATUS,OrderStatusRevision::FINISH_ORDER_BY_WORKER_STATUS];

        $orders = Order::whereIn('status',$activeStatus)->where('worker_id',$request->user()->id)->get();


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

        function listArchiveOrderWorker(Request $request)
    {
        $offset=0;
        $limit=30;
        if ($request->has('offset'))
            $offset=$request->input('offset');
        if ($request->has('limit'))
            $limit = $request->input('limit');

        $archiveStatus = [OrderStatusRevision::PAID_ORDER_BY_CLIENT_STATUS,OrderStatusRevision::CANCEL_ORDER_BY_CLIENT_STATUS,OrderStatusRevision::CANCEL_ORDER_BY_WORKER_STATUS];

        $orders = Order::whereIn('status',$archiveStatus)->skip($offset)->take($limit)->get();

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

        return response()->json(['orders'=>$orders]);

    }



        function detailOrderWorker(Request $request)
    {
        $order = Order::find($request->input('order_id'));

        $review = Review::where('order_id',new ObjectID($request->input('order_id')))->first();


        if ($review)
            $order->review=$review;
        else
            $order->review=false;
        if ($order->revisions)
        {
            $order->services=$order->revisions[0]['services'];
            unset($order->revisions);
        }


        return response()->json(['order'=>$order]);

    }

}

