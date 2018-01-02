<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\DissatisfiedReason;
use App\Order;
use App\OrderStatusRevision;
use App\Review;
use App\Service;
use App\ServiceQuestion;
use App\User;
use Couchbase\UserSettings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use MongoDB\BSON\ObjectID;
use Symfony\Component\Console\Question\Question;

class OrderController extends Controller
{


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin')->except('filterOrder');
    }


    function index()
    {

        $orders=Order::paginate(15);

        $ordersArr=[];
        foreach ($orders as $item)
        {


            $date = \Morilog\Jalali\jDateTime::strftime('d/m/Y', strtotime($item->created_at));


            $order=[];
            $order['user']=User::find($item->user_id);
            $order['created_at'] = \Morilog\Jalali\jDateTime::strftime('d/m/Y', strtotime($item->created_at));
            switch ($item->status)
            {
                case OrderStatusRevision::WAITING_FOR_WORKER_STATUS:
                    $order['status']='منتظر تایید خدمه';
                    break;
                case OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS:
                    $order['status']='قبول درخواست توسط خدمه';
                    break;
                case OrderStatusRevision::START_ORDER_BY_WORKER_STATUS:
                    $order['status']='شروع کار توسط خذمه';
                    break;
                case OrderStatusRevision::FINISH_ORDER_BY_WORKER_STATUS:
                    $order['status']='اتمام کار توسط خدمه';
                    break;
                case OrderStatusRevision::PAID_ORDER_BY_CLIENT_STATUS:
                    $order['status']='پرداخت توسط خدمه';
                    break;
                case OrderStatusRevision::CANCEL_ORDER_BY_CLIENT_STATUS:
                    $order['status']='لغو توسط مشتری';
                    break;
                case OrderStatusRevision::CANCEL_ORDER_BY_WORKER_STATUS:
                    $order['status']='لغو توسط خدمه';
                    break;

            }
            $order['total_price']=$item->total_price;
            $order['order_id']=$item->id;
            array_push($ordersArr,$order);
        }

        $data['orders']=$ordersArr;
        $data['categories']=Category::all();


        return view('admin.pages.order.list_order')->with($data);

    }

    function filterOrder(Request $request)
    {
        $content = $request->getContent();

        $content =(json_decode($content));


        $query=[];

        if (isset($content->tracking_number))
            $query['tracking_number']=(int)$content->tracking_number;
        if (isset($content->status))
            $query['status']=$content->status;
        if (isset($content->field))
            $query['category_id'] =$content->field;
        // dd($query);

        $q = [
            [ '$limit' => 10 ],
            [ '$sort' => ['_id' => -1], ],


            [
                '$match' => $query
            ]


        ];
        $model = Order::raw()->aggregate($q);
//
        $orderArr=[];
        $i=0;
        foreach ($model as $item)
        {

            $order=[];
            $order['user']=User::find($item['user_id']);
            $order['created_at'] = \Morilog\Jalali\jDateTime::strftime('d/m/Y', strtotime($item['created_at']));
            switch ($item['status'])
            {
                case OrderStatusRevision::WAITING_FOR_WORKER_STATUS:
                    $order['status']='منتظر تایید خدمه';
                    break;
                case OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS:
                    $order['status']='قبول درخواست توسط خدمه';
                    break;
                case OrderStatusRevision::START_ORDER_BY_WORKER_STATUS:
                    $order['status']='شروع کار توسط خذمه';
                    break;
                case OrderStatusRevision::FINISH_ORDER_BY_WORKER_STATUS:
                    $order['status']='اتمام کار توسط خدمه';
                    break;
                case OrderStatusRevision::PAID_ORDER_BY_CLIENT_STATUS:
                    $order['status']='پرداخت توسط خدمه';
                    break;
                case OrderStatusRevision::CANCEL_ORDER_BY_CLIENT_STATUS:
                    $order['status']='لغو توسط مشتری';
                    break;
                case OrderStatusRevision::CANCEL_ORDER_BY_WORKER_STATUS:
                    $order['status']='لغو توسط خدمه';
                    break;

            }
            $order['total_price']=$item['total_price'];
           // dd((string)$item['_id']);
            $order['order_id']=(string)$item['_id'];
            $i++;
            //dd($order);
            $orderArr[]=$order;

            //array_push($ordersArr,1);
        }

     //   dd($i);




        return json_encode($orderArr,true);
    }

    function showDetailOrder($order_id)
    {
        $orderModel = Order::find($order_id);

        $order =[];
        $order['user']=User::find($orderModel->user_id);
        if ($orderModel->worker_id)
        {
            $order['worker']=User::find($orderModel->worker_id);
        }
        $order['address'] =$orderModel->address;
        $order['category'] = Category::find($orderModel->category_id);
        $order['total_price'] =$orderModel->total_price;
        $order['tracking_number'] =$orderModel->tracking_number;
        $order['created_at']=\Morilog\Jalali\jDateTime::strftime('Y/m/d H:i:s', strtotime($orderModel->created_at));
        switch ($orderModel->status)
        {
            case OrderStatusRevision::WAITING_FOR_WORKER_STATUS:
                $order['status']='منتظر تایید خدمه';
                break;
            case OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS:
                $order['status']='قبول درخواست توسط خدمه';
                break;
            case OrderStatusRevision::START_ORDER_BY_WORKER_STATUS:
                $order['status']='شروع کار توسط خذمه';
                break;
            case OrderStatusRevision::FINISH_ORDER_BY_WORKER_STATUS:
                $order['status']='اتمام کار توسط خدمه';
                break;
            case OrderStatusRevision::PAID_ORDER_BY_CLIENT_STATUS:
                $order['status']='پرداخت توسط خدمه';
                break;
            case OrderStatusRevision::CANCEL_ORDER_BY_CLIENT_STATUS:
                $order['status']='لغو توسط مشتری';
                break;
            case OrderStatusRevision::CANCEL_ORDER_BY_WORKER_STATUS:
                $order['status']='لغو توسط خدمه';
                break;

        }
        $services=[];
        foreach ($orderModel->services as $item)
        {

            $service =[];
            $service['entity'] =Service::find($item['service_id']);
            $service['unit_count'] =$item['unit_count'];
            $service['description']  =$item['description'];
            $service['price'] =$item['price'];
            $questions =[];

            $questionsId=array_keys($item['questions']);
            $questionsValue=array_values($item['questions']);


            for ($i=0;$i<count($questionsId);$i++)
            {
                $question['text']=ServiceQuestion::find($questionsId[$i])['questions'];
                $question['answer'] =$questionsValue[$i];
                $questions[]=$question;
            }
            $service['questions'] =$questions;
            $services[]=$service;

        }

        $order['services']=$services;
        $order['services'][0]['entity'];
//
        $data['order']=$order;
        $reviewModel = Review::where('order_id',new ObjectID($order_id))->first();

        $review['score']=$reviewModel->score;
        $review['desc'] =$reviewModel->desc;
        $reasons=[];
        foreach ($reviewModel->reasons as $item)
        {
            $reason=DissatisfiedReason::find($item)['reason'];
            $reasons[]=$reason;
        }
        $review['reasons']=$reasons;

      //  dd($review);

        $data['review']=$review;

        $revisionModel =OrderStatusRevision::where('order_id',new ObjectID($order_id))->get();

        $revisions=[];

        foreach ($revisionModel as $item)
        {
            $revision=[];
            switch ($item->status)
            {
                case OrderStatusRevision::WAITING_FOR_WORKER_STATUS:
                    $order['status']='منتظر تایید خدمه';
                    break;
                case OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS:
                    $order['status']='قبول درخواست توسط خدمه';
                    break;
                case OrderStatusRevision::START_ORDER_BY_WORKER_STATUS:
                    $order['status']='شروع کار توسط خذمه';
                    break;
                case OrderStatusRevision::FINISH_ORDER_BY_WORKER_STATUS:
                    $order['status']='اتمام کار توسط خدمه';
                    break;
                case OrderStatusRevision::PAID_ORDER_BY_CLIENT_STATUS:
                    $order['status']='پرداخت توسط خدمه';
                    break;
                case OrderStatusRevision::CANCEL_ORDER_BY_CLIENT_STATUS:
                    $order['status']='لغو توسط مشتری';
                    break;
                case OrderStatusRevision::CANCEL_ORDER_BY_WORKER_STATUS:
                    $order['status']='لغو توسط خدمه';
                    break;

            }
            $revision['created_at']=\Morilog\Jalali\jDateTime::strftime('Y/m/d H:i:s', strtotime($item->created_at));
            $revision['whom']   =User::find($item->whom);
            $revisions[]=$revision;

        }

        $data['revisions']=$revisions;



        return view('admin.pages.order.detail_order')->with($data);



    }
}
