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

class SendNotificationToSingleUserJobWithFcm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id;
    protected $data;
    protected $message;
    protected $title;

    /**
     * Create a new job instance.
     * @param $user_id
     * @param $message
     * @param $title
     * @param $data
     *
     * @return void
     */
    public function __construct( $user_id, $message = null, $title = null, $data = null)
    {

        $this->user_id = $user_id;
        $this->message = $message;
        $this->title = $title;
        $this->data = $data;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $user=User::find($this->user_id);



        $fcm_token = $user->fcm_token;
//        dd($fcm_token);

        $content = array(
            "en" => $this->message
        );
        $fields = array(
            'app_id' => "5cf31f6e-0526-4083-841b-03d789183ab8",
            'include_player_ids' => array($fcm_token),
            'data' => $this->data,
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
//        $optionBuilder->setTimeToLive(60 * 20);
//
//        $notificationBuilder = new PayloadNotificationBuilder($this->title);
//        $notificationBuilder->setBody($this->message)->setSound('default');
//
//        $dataBuilder = new PayloadDataBuilder();
//        $dataBuilder->addData(['data' => $this->data]);
//
//        $option = $optionBuilder->build();
//        $notification = $notificationBuilder->build();
//        $data = $dataBuilder->build();
//
//        $token = $this->user->fcm_token;
//
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


    }
}