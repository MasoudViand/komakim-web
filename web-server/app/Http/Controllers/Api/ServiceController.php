<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\Jobs\CheckServiceAccepted;
use App\Jobs\FindWorker;
use App\Jobs\RegisterStatusOrderRevisionJob;
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
use Illuminate\Support\Facades\URL;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;

class ServiceController extends Controller
{
    function listcategory()
    {
        $categories = Category::where('status',true)->get(['id','name'])->sortBy('order');



        return response()->json(['category'=>$categories]);

    }

    function listservice(Request $request)
    {
        if (!$request->has('category_id'))
            return response()->json(['error'=>'category id is require'])->setStatusCode('417');

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

                if (!empty($services['description']))
                {
                    $tempservice['description']=(string)$service['description'];

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
            return response()->json(['error'=>'address is invalid'])->setStatusCode('417');
        if(!property_exists($order, 'services') || !is_array($order->services))
            return response()->json(['error'=>'services is invalid'])->setStatusCode('417');

        if(!property_exists($order, 'total_price'))
            return response()->json(['error'=>'total_price is invalid'])->setStatusCode('417');

        $order->status =OrderStatusRevision::WAITING_FOR_WORKER_STATUS;
        $user = $request->user();
        $order->user_id =$user->id;
        $order->created_at=new UTCDateTime(time()*1000);
        $prevOrder = Order::orderby('_id','desc')->first();
        if (!$prevOrder)
            $tracking_number =1000;
        else
            $tracking_number =(int)$prevOrder->tracking_number+1;

        $order->tracking_number=(string)$tracking_number;

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
            return response()->json(['error'=>'order_id is required'])->setStatusCode(417);


         $order = Order::find($request->input('order_id'));




         if (!($order['status']==OrderStatusRevision::WAITING_FOR_WORKER_STATUS))
             return response()->json(['error'=>'این سفارش توسط خدمه دیگری مورد توافق قرار گرفته اشت'])->setStatusCode(423);



         $order->worker_id=$request->user()->id;
         $order->status   =OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS;

         if ($order->save())
         {
              $order =$this->_sendNotificationToClient($order->user_id,$order);
              $this->dispatch(new RegisterStatusOrderRevisionJob($order->id,OrderStatusRevision::ACCEPT_ORDER_BY_WORKER_STATUS,$request->user()) );

              return response()->json(['order'=>$order]);
         }else
         {
             return response()->json(['error'=>'internal error'])->setStatusCode(500);
         }


    }


    function startOrder(Request $request)
    {
        if (!$request->has('order_id')){
            return response()->json(['error'=>'order id is require'])->setStatusCode(417);
        }
        $order = Order::find($request->input('order_id'));


        $order->status =OrderStatusRevision::START_ORDER_BY_WORKER_STATUS;

        if ($order->save())
        {
            $this->dispatch(new RegisterStatusOrderRevisionJob($order->id,OrderStatusRevision::START_ORDER_BY_WORKER_STATUS,$request->user()) );

            //$this->_sendNotificationToClient();

            return response()->json(['order'=>$order]);

        }else
        {
            return response()->json(['error'=>'internal server error'])->setStatusCode(500);
        }
    }

    function claimFinishOrderByWorker(Request $request)
    {

        if (!$request->has('order_id')){
            return response()->json(['error'=>'order id is require'])->setStatusCode(417);
        }
        $order = Order::find($request->input('order_id'));
        $order->status =OrderStatusRevision::FINISH_ORDER_BY_WORKER_STATUS;

        if ($order->save())
        {
            $this->dispatch(new RegisterStatusOrderRevisionJob($order->id,OrderStatusRevision::FINISH_ORDER_BY_WORKER_STATUS,$request->user()) );

            //$this->_sendNotificationToClient();

            return response()->json(['order'=>$order]);

        }else
        {
            return response()->json(['error'=>'internal server error'])->setStatusCode(500);
        }

    }


    private function _sendNotificationToClient($user_id ,$order)
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder('خدمه یافت شد');
        $notificationBuilder->setBody('')
            ->setSound('default');



        $dataBuilder = new PayloadDataBuilder();

        $category=Category::find($order['category_id']);
        $order['category']=$category;
        unset($category['order']);unset($category['status']);unset($category['updated_at']);unset($category['created_at']);

        unset($order['category_id']);

        $workerProfile = User::find($order->worker_id);

        $profile =[];
        $filepath=null;

        $profile['name']=$workerProfile['name'];
        $profile['family']=$workerProfile['family'];
        $profile['mobile_number']=$workerProfile['phone_number'];
        if (file_exists((public_path('images/workers').'/'.$workerProfile->id).'.png'))
            $filepath=('images/workers').'/'.$workerProfile->id.'.png';
        if (file_exists((public_path('images/workers').'/'.$workerProfile->id).'.jpg'))
            $filepath=('images/workers').'/'.$workerProfile->id.'.jpg';
        if (file_exists((public_path('images/workers').'/'.$workerProfile->id).'.jpeg'))
            $filepath=('images/workers').'/'.$workerProfile->id.'.jpeg';

        $profile['picture_profile_url']=URL::to('/').'/'.$filepath;

        $order['profile']=$profile;

        //dd($profile['picture_profile_url']);

        return $order;



        $dataBuilder->addData(['order'=>$order]);




        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();





//        $downstreamResponse = FCM::sendTo($clientToken, $option, $notification, $data);
//
//        $downstreamResponse->numberSuccess();
//        $downstreamResponse->numberFailure();
//        $downstreamResponse->numberModification();
//
////return Array - you must remove all this tokens in your database
//        $downstreamResponse->tokensToDelete();
//
////return Array (key : oldToken, value : new token - you must change the token in your database )
//        $downstreamResponse->tokensToModify();
//
////return Array - you should try to resend the message to the tokens in the array
//        $downstreamResponse->tokensToRetry();

// return Array (key:token, value:errror) - in production you should remove from your database the
    }


}
