<?php

namespace App\Jobs;

use App\Category;
use App\Order;
use App\Service;
use App\User;
use App\WorkerProfile;
use function GuzzleHttp\Psr7\str;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use MongoDB\Operation\Find;
use function PHPSTORM_META\type;
use stdClass;

class FindWorker implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     * @param $order
     *
     * @return void
     */
    public function __construct( Order $order)
    {


        $this->order=$order;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle( )
    {


        $category_id =$this->order['category_id'];
        $latitude =$this->order['address']['latitude'];
        $longitude =$this->order['address']['longitude'];


        $workersIds = WorkerProfile::where('location', 'near', [
            '$geometry' => [
                'type' => 'Point',
                'coordinates' => [
                    $latitude, $longitude
                ],
            ],
            '$maxDistance' => 5000,
        ])->where('field' ,$category_id)->where('availability_status', 'available')->get(['user_id']);
        //dd($category_id);


        if (count($workersIds)>0)
        {

            $userId=[];

            foreach ($workersIds as $item){
                array_push($userId,(string)$item['user_id']);

            }



            $userToken =User::find($userId);
            $tokens=[];

            foreach ($userToken as $item)
            {
                if ($item['fcm_token'])
                    array_push($tokens,$item['fcm_token']);

            }




        }else
        {

            $clientUserId=$this->order['user_id'];

            $clientUser=User::find($clientUserId);


            $clientToken=$clientUser['fcm_token'];

            $this->_sendFailFindWorkerNotificationToClient($clientToken);

            //Todo send data notification to user
        }







    }

    private function _sendNotifications($tokens)
    {

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder('شفارش جدید');
        $notificationBuilder->setBody('')
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $order=$this->order;
        $category=Category::find($order['category_id']);
        $order['category']=$category;
        unset($category['order']);unset($category['status']);unset($category['updated_at']);unset($category['created_at']);

        unset($order['category_id']);
        $dataBuilder->addData(['data' => $order]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();


        $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

        $downstreamResponse->tokensToDelete();

        $downstreamResponse->tokensToModify();

        $downstreamResponse->tokensToRetry();

        $downstreamResponse->tokensWithError();
    }

    private function _sendFailFindWorkerNotificationToClient($clientToken)
    {

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder('خدمه یافت نشد');
        $notificationBuilder->setBody('')
            ->setSound('default');



        $dataBuilder = new PayloadDataBuilder();
        $order=$this->order;
        $category=Category::find($order['category_id']);
        $order['category']=$category;
        unset($category['order']);unset($category['status']);unset($category['updated_at']);unset($category['created_at']);

        unset($order['category_id']);



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
