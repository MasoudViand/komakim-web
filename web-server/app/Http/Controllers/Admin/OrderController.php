<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Category;
use App\DissatisfiedReason;
use App\Jobs\RegisterStatusOrderRevisionJob;
use App\Jobs\SendNotificationToSingleUserJobWithFcm;
use App\Jobs\SendSmsToSingleUser;
use App\Order;
use App\OrderStatusRevision;
use App\Review;
use App\Service;
use App\ServiceQuestion;
use App\User;
use App\WorkerProfile;
use Couchbase\UserSettings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\HtmlString;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;
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
        $this->middleware(['auth:admin','operator'])->except('filterOrder');
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
            ['$sort'=>['_id'=>-1]],
            [ '$skip'  =>$skip ],
            [ '$limit' => 20 ],

        ];
        $q_count=[];
        if (count($query)>0)
        {
            $q = [
                ['$sort'=>['_id'=>-1]],
                ['$match' => $query],
                [ '$skip'  =>$skip ],
                [ '$limit' => 20 ],

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

            $tehrantime = $item['created_at']->toDateTime();
            $tehrantime->setTimeZone(new \DateTimeZone('Asia/Tehran'));



            $order=[];
            $order['user']=User::find($item->user_id);
            $order['created_at'] = \Morilog\Jalali\jDateTime::strftime('d/m/Y', $item['created_at']->toDateTime());
            $order['created_at_hour']=\Morilog\Jalali\jDateTime::strftime('h:i', $tehrantime);

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
                case OrderStatusRevision::EDIT_BY_WORKER_STATUS:
                    $order['status'] ='ویرایش توسط خدمه';
                    break;

                case OrderStatusRevision::CANCEL_ORDER_BY_ADMIN_STATUS:
                    $order['status']='لغو توسط ادمین';
                    break;
                case OrderStatusRevision::NOT_FOUND_WORKER_STATUS:
                    $order['status']='عدم یافت خدمه';
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


        if (isset($queryParam['status']))
        {
            switch ($queryParam['status'])
            {
                case OrderStatusRevision::WAITING_FOR_WORKER_STATUS:
                    $queryParam['status_plain_text']='منتظر تایید خدمه';
                    break;
                case OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS:
                    $queryParam['status_plain_text']='قبول درخواست توسط خدمه';
                    break;
                case OrderStatusRevision::START_ORDER_BY_WORKER_STATUS:
                    $queryParam['status_plain_text']='شروع کار توسط خذمه';
                    break;
                case OrderStatusRevision::FINISH_ORDER_BY_WORKER_STATUS:
                    $queryParam['status_plain_text']='اتمام کار توسط خدمه';
                    break;
                case OrderStatusRevision::PAID_ORDER_BY_CLIENT_STATUS:
                    $queryParam['status_plain_text']='پرداخت توسط خدمه';
                    break;
                case OrderStatusRevision::CANCEL_ORDER_BY_CLIENT_STATUS:
                    $queryParam['status_plain_text']='لغو توسط مشتری';
                    break;
                case OrderStatusRevision::CANCEL_ORDER_BY_WORKER_STATUS:
                    $queryParam['status_plain_text']='لغو توسط خدمه';
                    break;
                case OrderStatusRevision::EDIT_BY_WORKER_STATUS:
                    $queryParam['status_plain_text'] ='ویرایش توسط خدمه';
                    break;

                case OrderStatusRevision::CANCEL_ORDER_BY_ADMIN_STATUS:
                    $queryParam['status_plain_text']='لغو توسط ادمین';
                    break;
                case OrderStatusRevision::NOT_FOUND_WORKER_STATUS:
                    $queryParam['status_plain_text']='عدم یافت خدمه';
                    break;


            }
        }


        $data['total_page']=(int)($data['count']/20)+1;
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
            case OrderStatusRevision::NOT_FOUND_WORKER_STATUS:
                $order['status']='عدم یافت خدمه';
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
            case OrderStatusRevision::EDIT_BY_WORKER_STATUS:
                $order['status'] ='ویرایش توسط خدمه';
                break;
            case OrderStatusRevision::CANCEL_ORDER_BY_ADMIN_STATUS:
                $order['status']='لغو توسط ادمین';
                $order['cancel_reason']=$orderModel->cancel_reason;
                break;

        }
        $services=[];

        if ($orderModel->revisions)
            $servicesModel=$orderModel->revisions[0]['services'];
        else
            $servicesModel=$orderModel->services;


        foreach ($servicesModel as $item)
        {

            $service =[];

            $service['entity'] =Service::find($item['service_id']);
            $service['unit_count'] =$item['unit_count'];
            $service['description']  =$item['description'];
            $service['price'] =$item['price'];
            $questions =[];
            if (key_exists('questions',$item))
            {
                $questionsId=array_keys($item['questions']);
                $questionsValue=array_values($item['questions']);


                for ($i=0;$i<count($questionsId);$i++)
                {
                    $question['text']=ServiceQuestion::find($questionsId[$i])['questions'];
                    $question['answer'] =$questionsValue[$i];
                    $questions[]=$question;
                }
                $service['questions'] =$questions;
            }


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

      //  dd(!is_null($revisionModel[1]['whom']));




        $revisions=[];

        foreach ($revisionModel as $item)
        {
            $revision=[];
            switch ($item->status)
            {
                case OrderStatusRevision::WAITING_FOR_WORKER_STATUS:
                    $revision['status']='منتظر تایید خدمه';
                    break;
                case OrderStatusRevision::NOT_FOUND_WORKER_STATUS:
                    $revision['status']='عدم یافت خدمه';
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
                case OrderStatusRevision::EDIT_BY_WORKER_STATUS:
                    $revision['status'] ='ویرایش توسط خدمه';
                    break;
                case OrderStatusRevision::APPROVE_EDIT_BY_CLIENT_STATUS:
                    $revision['status'] ='قبول ویرایش توسط مشتری';
                    break;

                case OrderStatusRevision::CANCEL_ORDER_BY_ADMIN_STATUS:
                    $revision['status']='لغو توسط ادمین';
                    break;

            }

            $revision['created_at']=\Morilog\Jalali\jDateTime::strftime('Y/m/d H:i:s', strtotime($item->created_at));

            if (!is_null($item->whom))
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
            $revision = new \stdClass();
            $revision->order_id=($order->order_id);
            $revision->created_at = new UTCDateTime(time()*1000);
            $revision->status=OrderStatusRevision::CANCEL_ORDER_BY_ADMIN_STATUS;
            $revision->whom =Admin::find($request->user()->id);
            $model = OrderStatusRevision::raw()->insertOne($revision);
            $worker_id = $order->worker_id;
            $user_id = $order->user_id;

            $user = User::find($user_id);
            $user_number = $user->phone_number;
            $message='سفارش با کد پیگیری '.$order->tracking_number.'توسط کمکیم لغو شد';
            $this->dispatch(new SendSmsToSingleUser($user_number,$message));


            if ($worker_id)
            {
                $worker = User::find($worker_id);
                $worker_number = $worker->phone_number;
                $this->dispatch(new SendSmsToSingleUser($worker_number,$message));
                $this->dispatch(new SendNotificationToSingleUserJobWithFcm($order->worker_id,'لغو توسط ادمین','',$order,User::WORKER_ROLE));
                $workerProfile =WorkerProfile::where('user_id',$worker_id)->first();
                $workerProfile->has_active_order=false;
                $workerProfile->save();
            }
            $this->dispatch(new SendNotificationToSingleUserJobWithFcm($order->user_id,'لغو توسط ادمین','',$order,User::CLIENT_ROLE));
            $this->dispatch(new RegisterStatusOrderRevisionJob($order->id,OrderStatusRevision::CANCEL_ORDER_BY_WORKER_STATUS,$request->user()));
            return redirect()->back();


        }
        else return redirect()->with(['error'=>'لغو نشد']);

    }

}


