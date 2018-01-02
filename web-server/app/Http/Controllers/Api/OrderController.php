<?php

namespace App\Http\Controllers\Api;

use App\Order;
use App\OrderStatusRevision;
use App\Review;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mockery\Tests\React_WritableStreamInterface;
use MongoDB\BSON\ObjectID;


class OrderController extends Controller
{





        function listActiveOrder(Request $request)

        {
           // dd($request->user()->id);
            $activeStatus=[OrderStatusRevision::WAITING_FOR_WORKER_STATUS,OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS,OrderStatusRevision::START_ORDER_BY_WORKER_STATUS,OrderStatusRevision::FINISH_ORDER_BY_WORKER_STATUS];
           // dd($acceptStatus);
            $orders = Order::whereIn('status',$activeStatus)->where('user_id',$request->user()->id)->get();


            foreach ($orders as $item)
            {

                if ($item->worker_id)
                    $item->worker=User::find($item->worker_id);
                else
                    $item->worker=false;

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

            }

            return response()->json(['orders'=>$orders]);

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


        return response()->json(['order'=>$order]);

    }

}

