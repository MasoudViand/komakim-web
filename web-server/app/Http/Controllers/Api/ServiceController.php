<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\Jobs\CheckServiceAccepted;
use App\Jobs\FindWorker;
use App\Order;
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
            $services =($item['service']);
            $serviceArr=[];

            foreach ($services as $service){

                $tempservice=[];
                $tempservice['_id']=(string)$service['_id'];
                $tempservice['name']=(string)$service['name'];
                $tempservice['price']=(string)$service['price'];
                $tempservice['minimum_number']=(string)$service['minimum_number'];
                $tempservice['description']=(string)$service['description'];
                $servicequestions=ServiceQuestion::where('service_id',(string)$service['_id'])->get(['questions','id']);

                $tempservice['questions']=$servicequestions;


                array_push($serviceArr,$tempservice);

            }
            $subcategory['services']=$serviceArr;

        }


        return response()->json(['subcategory'=>$subcategory]);
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

        $order->status ='pending';
        $order->user_id =$request->user()->id;


        $model = Order::raw()->insertOne($order);

        $order=Order::find((string)($model->getInsertedId()));


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
         $order =Order::find($request->input('order_id'));


         if (!($order['status']=='pending'))
             return response()->json(['error'=>'order accept from another worker'])->setStatusCode(423);



         $order->worker_id=$request->user()->id;
         $order->status   ='accept';

         if ($order->save())
         {
              $order =$this->_sendNotificationToClient($order->user_id,$order);

              return response()->json(['order'=>$order]);
         }else
         {
             return response()->json(['error'=>'internal error'])->setStatusCode(500);
         }


    }


    function searchWorker(Request $request)
    {

        $location = new  \stdClass();

        $location->type ="point";
        $location->coordinates =[-110.8571443, 32.4586858 ];




     //  $user = DB::collection('worker_profiles')->where('user_id', new ObjectID('5a3a3571978ef406071f6e64'))->update(['location'=>$location], ['upsert' => true]);
//
   //    dd($user);

        $users = WorkerProfile::where('location', 'near', [
            '$geometry' => [
                'type' => 'Point',
                'coordinates' => [
                    -73.9667, 40.78
                ],
            ],
            '$maxDistance' => 5000,
        ])->get();

        dd($users);
         $worker =WorkerProfile::where('field', "1")->where('user_id','werwerwerwer')->where('location', 'near', [
             '$geometry' => [
                 'type' => 'Point',
                 'coordinates' => [
                     -0.1367563,
                     51.5100913,
                 ],
             ],
             '$maxDistance' => 50,
         ])->get();


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
