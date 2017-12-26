<?php

namespace App\Jobs;

use App\Order;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

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
        if ($order['status']=='pending'){
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
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder('خدمه یافت نشد');
        $notificationBuilder->setBody('')
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['data'=>$order]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

//        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
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

// return Array (key:token, value:errror) - in production you should remove from your database the tokens
    }
}
