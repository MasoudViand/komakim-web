<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\Jobs\CheckServiceAccepted;
use App\Jobs\FindWorker;
use App\Jobs\RegisterStatusOrderRevisionJob;
use App\Jobs\SendNotificationToSingleUserJobWithFcm;
use App\Jobs\SendSmsToSingleUser;
use App\Order;
use App\OrderPayment;
use App\OrderStatusRevision;
use App\Service;
use App\ServiceQuestion;
use App\Subcategory;
use App\User;
use App\WorkerProfile;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\URL;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;
use function MongoDB\is_first_key_operator;

class ServiceController extends Controller
{
    function listcategory()
    {
        $categories = Category::where('status',true)->get(['id','name'])->sortBy('order');
        foreach ($categories as $category)
        {
            $filepath =  '/images/icons/service-icon-default.jpg';


            if (file_exists((public_path('images/icons') . '/' . $category->id) . '.png')) $filepath = ('/images/icons') . '/' . $category->id . '.png';
            if (file_exists((public_path('images/icons') . '/' . $category->id) . '.jpg')) $filepath = ('/images/icons') . '/' . $category->id . '.jpg';
            if (file_exists((public_path('images/icons') . '/' . $category->id) . '.jpeg')) $filepath = ('/images/icons') . '/' . $category->id . '.jpeg';

            $filepath=URL::to('/') .''.$filepath;

            $category->filepath = $filepath;
        }



        return response()->json(['category'=>$categories]);

    }

    function listservice(Request $request)
    {
        if (!$request->has('category_id'))
            return response()->json(['errors'=>'category id is require'])->setStatusCode('417');

        $q = [

            [ '$sort' => ['order' => -1], ],

            [ '$lookup' => [
                'from'         => 'services',
                'localField'   => '_id',
                'foreignField' => 'subcategory_id',
                'as'           => 'service',],

            ],

            [
                '$match'=> [
                    'category_id' =>$request->input('category_id')
                ]
            ],
            [
                '$project' => [
                    '_id' => 1,
                    'name'=>1,
                    'service' => [
                        '$filter' => [
                            'input' => '$service',
                            'as'    => 'service',
                            'cond'  => [

                            ],
                        ],
                    ],
                ],
            ],


        ];
        $model = Subcategory::raw()->aggregate($q);

        $subcategoriesArr=[];

        foreach ($model as $item)
        {
            
            $subcategory['_id']=(string)$item['_id'];
            $subcategory['name']=$item['name'];
            $serviceArr=[];

            foreach ($item['service'] as $service){



                $tempservice=[];
                $tempservice['_id']=(string)$service['_id'];
                $tempservice['name']=(string)$service['name'];
                $tempservice['price']=(string)$service['price'];
                $tempservice['minimum_number']=(string)$service['minimum_number'];
                $tempservice['unit']=(string)$service['unit'];


                if (!empty($service['description']))
                {
                    $tempservice['description']=(string)$service['description'];

                }else
                {
                    $tempservice['description']=null;
                }


                $servicequestions=ServiceQuestion::where('service_id',(string)$service['_id'])->get(['questions','id']);

                $tempservice['questions']=$servicequestions;




                array_push($serviceArr,$tempservice);

            }

            $subcategory['services']=$serviceArr;

            $subcategoriesArr[]=$subcategory;

        }


        return response()->json(['subcategories'=>$subcategoriesArr]);
    }


    function registerOrder(Request $request)
    {

        $order = $request->getContent();


        $order =(json_decode($order));
        if(!property_exists($order, 'address') )
            return response()->json(['errors'=>'address is invalid'])->setStatusCode('417');
        if(!property_exists($order, 'services') || !is_array($order->services))
            return response()->json(['errors'=>'services is invalid'])->setStatusCode('417');

        if(!property_exists($order, 'total_price'))
            return response()->json(['errors'=>'total_price is invalid'])->setStatusCode('417');

        $order->status =OrderStatusRevision::WAITING_FOR_WORKER_STATUS;
        $user = $request->user();
        $order->user_id =new ObjectID($user->id);
        $order->created_at=new UTCDateTime(time()*1000);
        $prevOrder = Order::orderby('_id','desc')->first();
        if (!$prevOrder)
            $tracking_number =1000;
        else
            $tracking_number =(int)$prevOrder->tracking_number+1;

        $order->tracking_number=(string)$tracking_number;

        $order->revisions =[['status'=>Order::ACCEPTED_SERVICE_STATUS,'services'=>$order->services,'total_price'=>$order->total_price,'created_at'=>new UTCDateTime(time()*1000),'tracking_number'=>$order->tracking_number]];




        $model = Order::raw()->insertOne($order);

        $order=Order::find((string)($model->getInsertedId()));

        $this->dispatch(new RegisterStatusOrderRevisionJob($order->id,OrderStatusRevision::WAITING_FOR_WORKER_STATUS,$user));

        if ($model){
            $this->dispatch(new FindWorker($order));

            $job = (new CheckServiceAccepted((string)$order['_id']))->delay(60);
             $this->dispatch($job);
            return response()->json(['order'=>$order]);

        }

    }

    function acceptOrder(Request $request)
    {


        if(!$request->has('order_id'))
            return response()->json(['errors'=>'order_id is required'])->setStatusCode(417);


         $order = Order::find($request->input('order_id'));





         if (!($order['status']==OrderStatusRevision::WAITING_FOR_WORKER_STATUS))
             return response()->json(['errors'=>'این سفارش توسط خدمه دیگری مورد توافق قرار گرفته اشت'])->setStatusCode(420);


        $userId = $request->user()->id;
         $order->worker_id=new ObjectID($userId);
         $order->status   =OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS;

         if ($order->save())
         {

             $workerProfile=WorkerProfile::where('user_id',new ObjectID($userId))->first();
             $workerProfile->has_active_order=true;

             if ($workerProfile->save())
             {
                 //$order =$this->_sendNotificationToClient($order->user_id,$order);
                 $this->dispatch(new SendNotificationToSingleUserJobWithFcm($order->user_id,'تایید خدمه','',$order,User::CLIENT_ROLE));
                 $this->dispatch(new RegisterStatusOrderRevisionJob($order->id,OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS,$request->user()) );
                 return response()->json(['order'=>$order]);

             }else
             {
                 return response()->json(['errors'=>'internal error'])->setStatusCode(500);
             }

         }else
         {
             return response()->json(['errors'=>'internal errors'])->setStatusCode(500);
         }


    }


    function startOrder(Request $request)
    {
        if (!$request->has('order_id')){
            return response()->json(['errors'=>'order id is require'])->setStatusCode(417);
        }
        $order = Order::find($request->input('order_id'));

        if ($order['worker_id'] and (string)$order['worker_id']!=$request->user()->id)
            return response()->json(['errors'=>'این سفارش به شما تعلق ندارد'])->setStatusCode(420);


        if (!($order['status']==OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS))
            return response()->json(['errors'=>'وضعیت سفارش قبول شده توسط کاربر نیست'])->setStatusCode(420);



        $order->status =OrderStatusRevision::START_ORDER_BY_WORKER_STATUS;

        if ($order->save())
        {
            $this->dispatch(new RegisterStatusOrderRevisionJob($order->id,OrderStatusRevision::START_ORDER_BY_WORKER_STATUS,$request->user()) );
            $this->dispatch(new SendNotificationToSingleUserJobWithFcm($order->user_id,'شروع به کار خدمه','',$order,User::CLIENT_ROLE));


            //$this->_sendNotificationToClient();

            return response()->json(['order'=>$order]);

        }else
        {
            return response()->json(['errors'=>'internal server errors'])->setStatusCode(500);
        }
    }

    function claimFinishOrderByWorker(Request $request)
    {

        if (!$request->has('order_id')){
            return response()->json(['errors'=>'order id is require'])->setStatusCode(417);
        }
        $order = Order::find($request->input('order_id'));

        if ($order['worker_id'] and (string)$order['worker_id']!=$request->user()->id)
            return response()->json(['errors'=>'این سفارش به شما تعلق ندارد'])->setStatusCode(420);


        if (!($order['status']==OrderStatusRevision::START_ORDER_BY_WORKER_STATUS))
            return response()->json(['errors'=>'وضعیت سفارش شروع شده توسط کاربر نیست'])->setStatusCode(420);



        $order->status =OrderStatusRevision::FINISH_ORDER_BY_WORKER_STATUS;

        if ($order->save())
        {
            $workerProfile=WorkerProfile::where('user_id',new ObjectID($request->user()->id))->first();
            $workerProfile->has_active_order=false;
            $workerProfile->save();

            $user_id=$order->user_id;
            $user =User::find($user_id);
            $phone_number=$user->phone_number;
            $message='سفارش با کد پیگیری '.$order->tracking_number.' به پایان رسید و در انتظار پرداخت شماست. کمکیم';

            $this->dispatch(new SendSmsToSingleUser($phone_number,$message));
            $this->dispatch(new RegisterStatusOrderRevisionJob($order->id,OrderStatusRevision::FINISH_ORDER_BY_WORKER_STATUS,$request->user()) );
            $this->dispatch(new SendNotificationToSingleUserJobWithFcm($order->user_id,'اتمام کار خدمه','',$order,User::CLIENT_ROLE));

            return response()->json(['order'=>$order]);

        }else
        {
            return response()->json(['errors'=>'internal server errors'])->setStatusCode(500);
        }

    }





}
