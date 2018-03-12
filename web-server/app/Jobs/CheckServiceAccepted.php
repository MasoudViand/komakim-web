<?php

namespace App\Jobs;

use App\Order;
use App\OrderStatusRevision;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;

class CheckServiceAccepted implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $order_id;

    /**
     * Create a new job instance.
     *
     * @param string $order_id
     *
     * @return void
     */
    public function __construct(  $order_id)
    {
        $this->order_id=$order_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {



        $order =Order::find($this->order_id);

        if ($order['status']==OrderStatusRevision::WAITING_FOR_WORKER_STATUS){
            $order->status =OrderStatusRevision::NOT_FOUND_WORKER_STATUS;
            $revision = new \stdClass();
            $revision->order_id=($order->order_id);
            $revision->created_at = new UTCDateTime(time()*1000);
            $revision->status=OrderStatusRevision::NOT_FOUND_WORKER_STATUS;
            $revision->whom =null;
            $model = OrderStatusRevision::raw()->insertOne($revision);


            $user_id=$order['user_id'];
            $user=User::find($user_id);
            $token=$user['fcm_token'];

            $this->_sendNotification($token ,$order);
        }


    }

    private function _rerrievefromOrder()
    {


    }

    private function _sendNotification($token,$order)
    {


        $content = array(
            "en" => 'خدمه یافت نشد'
        );
        $fields = array(
            'app_id' => "5cf31f6e-0526-4083-841b-03d789183ab8",
            'include_player_ids' => [$token],
            'data' => $order,
            'contents' => $content
        );
        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic Yzc0M2E3NzItYjZmMS00MDg4LWJiZDAtMjZkZWI4NDJmNDhi'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);




//        $optionBuilder = new OptionsBuilder();
//        $optionBuilder->setTimeToLive(60*20);
//
//        $notificationBuilder = new PayloadNotificationBuilder('خدمه یافت نشد');
//        $notificationBuilder->setBody('')
//            ->setSound('default');
//
//        $dataBuilder = new PayloadDataBuilder();
//        $dataBuilder->addData(['data'=>$order]);
//
//        $option = $optionBuilder->build();
//        $notification = $notificationBuilder->build();
//        $data = $dataBuilder->build();
//
////        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
////
////        $downstreamResponse->numberSuccess();
////        $downstreamResponse->numberFailure();
////        $downstreamResponse->numberModification();
////
//////return Array - you must remove all this tokens in your database
////        $downstreamResponse->tokensToDelete();
////
//////return Array (key : oldToken, value : new token - you must change the token in your database )
////        $downstreamResponse->tokensToModify();
////
//////return Array - you should try to resend the message to the tokens in the array
////        $downstreamResponse->tokensToRetry();
//
//// return Array (key:token, value:errror) - in production you should remove from your database the tokens
    }
}
