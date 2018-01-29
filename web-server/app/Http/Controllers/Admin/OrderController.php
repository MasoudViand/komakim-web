<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\DissatisfiedReason;
use App\Jobs\RegisterStatusOrderRevisionJob;
use App\Order;
use App\OrderStatusRevision;
use App\Review;
use App\Service;
use App\ServiceQuestion;
use App\User;
use Couchbase\UserSettings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\HtmlString;
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


    function index( Request $request)
    {
        $query=[];
        $queryParam=[];

        if ($request->has('page'))
        {
            $skip=((int)$request->input('page')-1)*10;
            $page = (int)$request->input('page');
        }
        else
        {
            $skip=0;
            $page=1;
        }
        if ($request->has('tracking_number'))
        {
            $query['tracking_number']=$request->input('tracking_number');

            $queryParam['tracking_number']=$request->input('tracking_number');

        }
        if ($request->has('status'))
        {
            $query['status']=$request->input('status');
            $queryParam['status']=$request->input('status');

        }
        if ($request->has('category_id'))
        {
            $query['category_id']=$request->input('category_id');
            $queryParam['category_id']=$request->input('category_id');
        }


        $q = [
            [ '$skip'  =>$skip ],
            [ '$limit' => 10 ],

        ];
        $q_count=[];
        if (count($query)>0)
        {
            $q = [
                ['$match' => $query],
                [ '$skip'  =>$skip ],
                [ '$limit' => 10 ],

            ];
            $q_count=[['$match' => $query]];
        }




        $q_count[]=['$count'=>'count'];





        $model = Order::raw()->aggregate($q);
        $count = Order::raw()->aggregate($q_count);





        $countArr=[];
        foreach ($count as $item)
        {
            array_push($countArr,$item);
        }


        $ordersArr=[];
        foreach ($model as $item)
        {
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

                case OrderStatusRevision::CANCEL_ORDER_BY_ADMIN_STATUS:
                    $order['status']='لغو توسط ادمین';
                    break;

            }

            $order['total_price']=$item->total_price;
            $order['order_id']=$item->_id;
            array_push($ordersArr,$order);
        }

        if (count($countArr)>0)
            $data['count']=$countArr[0]['count'];
        else
            $data['count']=0;

        $data['total_page']=(int)($data['count']/10)+1;
        $data['queryParam']=$queryParam;
        $data['page']= $page;
        $data['orders']     =$ordersArr;
        $data['categories'] =Category::all();
        $data['page_title']='لیست سفارشات';



        return view('admin.pages.order.list_order')->with($data);

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
        $order['id'] =$orderModel->id;
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
                $order['cancel_reason']=$orderModel->cancel_reason;
                break;
            case OrderStatusRevision::CANCEL_ORDER_BY_WORKER_STATUS:
                $order['status']='لغو توسط خدمه';
                $order['cancel_reason']=$orderModel->cancel_reason;
                break;
            case OrderStatusRevision::CANCEL_ORDER_BY_ADMIN_STATUS:
                $order['status']='لغو توسط ادمین';
                $order['cancel_reason']=$orderModel->cancel_reason;
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
        $data['review']=false;
        $reviewModel = Review::where('order_id',new ObjectID($order_id))->first();

        if ($reviewModel)
        {
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
        }


        $revisionModel =OrderStatusRevision::where('order_id',$order_id)->get();



        $revisions=[];

        foreach ($revisionModel as $item)
        {
            $revision=[];
            switch ($item->status)
            {
                case OrderStatusRevision::WAITING_FOR_WORKER_STATUS:
                    $revision['status']='منتظر تایید خدمه';
                    break;
                case OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS:
                    $revision['status']='قبول درخواست توسط خدمه';
                    break;
                case OrderStatusRevision::START_ORDER_BY_WORKER_STATUS:
                    $revision['status']='شروع کار توسط خذمه';
                    break;
                case OrderStatusRevision::FINISH_ORDER_BY_WORKER_STATUS:
                    $revision['status']='اتمام کار توسط خدمه';
                    break;
                case OrderStatusRevision::PAID_ORDER_BY_CLIENT_STATUS:
                    $revision['status']='پرداخت توسط خدمه';
                    break;
                case OrderStatusRevision::CANCEL_ORDER_BY_CLIENT_STATUS:
                    $revision['status']='لغو توسط مشتری';
                    break;
                case OrderStatusRevision::CANCEL_ORDER_BY_WORKER_STATUS:
                    $revision['status']='لغو توسط خدمه';
                    break;

                case OrderStatusRevision::CANCEL_ORDER_BY_ADMIN_STATUS:
                    $revision['status']='لغو توسط ادمین';
                    break;

            }

            $revision['created_at']=\Morilog\Jalali\jDateTime::strftime('Y/m/d H:i:s', strtotime($item->created_at));
            $revision['whom']   =User::find($item->whom);
            $revisions[]=$revision;


        }





        $data['revisions']=$revisions;
        $data['page_title']='دیدن جزییات سفارش';







        return view('admin.pages.order.detail_order')->with($data);



    }

    function CancelOrderByAdmin(Request $request)
    {


        $order = Order::find($request->input('idOrder'));

        if ($request->has('cancel_order_text'))
        $order->cancel_reason=$request->input('cancel_order_text');
        $order->status =OrderStatusRevision::CANCEL_ORDER_BY_ADMIN_STATUS;
        if ($order->save())
        {
            $this->dispatch(new RegisterStatusOrderRevisionJob($order->id,OrderStatusRevision::CANCEL_ORDER_BY_WORKER_STATUS,$request->user()));
            return redirect()->back();


        }
        else return redirect()->with(['error'=>'لغو نشد']);

    }

}


