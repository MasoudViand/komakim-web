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
use Illuminate\Support\Facades\URL;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use MongoDB\BSON\ObjectID;
use MongoDB\Operation\Find;
use function PHPSTORM_META\type;
use stdClass;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;

class SendNotificationToSingleUserJobWithFcm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id;
    protected $order;
    protected $message;
    protected $title;
    protected $target;

    /**
     * Create a new job instance.
     * @param $user_id
     * @param $message
     * @param $title
     * @param $order
     * @param $target
     */
    public function __construct( $user_id, $message = null, $title = null, $order = null ,$target=User::CLIENT_ROLE)
    {

        $this->user_id = $user_id;
        $this->message = $message;
        $this->title = $title;
        $this->order = $order;
        $this->target=$target;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {






        $user=User::find($this->user_id);








        if ($this->target==User::WORKER_ROLE)
        {

            $clientUserModel =User::find($this->order['user_id']);
            $clientUser['phone_number']=$clientUserModel->phone_number;
            $clientUser['name']=$clientUserModel->name;
            $clientUser['family']=$clientUserModel->family;

            $data=['order'=>$this->order,'user'=>$clientUser];
            $app_id ='aae19cb0-6ce8-44e9-b40d-8038799b952e';
            $authorization = 'Authorization: Basic MzM4MjM1ZjktZDE1Ni00NDg2LWEyZWYtNDUxNDNlZmJmYWYy';

        }
        else
        {



            $workerUserModel =User::find($this->order['worker_id']);


            $workerUser['phone_number']=$workerUserModel->phone_number;
            $workerUser['name']=$workerUserModel->name;
            $workerUser['family']=$workerUserModel->family;
            $workerProfile=WorkerProfile::where('user_id',new ObjectID($workerUserModel->id))->first();

            $url_image =  '/images/workers/profile-default-male.png';
            if (file_exists((public_path('images/workers') . '/' . $workerProfile->id) . '.png')) $url_image = ('/images/workers') . '/' . $workerProfile->id . '.png';
            if (file_exists((public_path('images/workers') . '/' . $workerProfile->id) . '.jpg')) $url_image = ('/images/workers') . '/' . $workerProfile->id . '.jpg';
            if (file_exists((public_path('images/workers') . '/' . $workerProfile->id) . '.jpeg')) $url_image = ('/images/workers') . '/' . $workerProfile->id . '.jpeg';

            $url_image=URL::to('/').''.$url_image;

            $workerUser['url_image'] = $url_image;

            $data=['order'=>$this->order ,'user'=>$workerUser];
            $app_id= '5cf31f6e-0526-4083-841b-03d789183ab8';
            $authorization = 'Authorization: Basic Yzc0M2E3NzItYjZmMS00MDg4LWJiZDAtMjZkZWI4NDJmNDhi';


        }



        $fcm_token = $user->fcm_token;

        $content = array(
            "en" => $this->message
        );


        $fields = array(
            'app_id' => $app_id,
            'include_player_ids' => array($fcm_token),
            'data' => $data,
            'contents' => $content
        );
        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            $authorization));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

    }
}
