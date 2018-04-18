<?php

namespace App\Jobs;

use App\Category;
use App\Order;
use App\Service;
use App\Setting;
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

        $category=Category::find($category_id);


        $distanc =Setting::where('type','radius')->first();
        if ($distanc)
            $distanc =(int)$distanc->value;
        else
            $distanc = 9000;


       // dd($category->name);


        $workersIds = WorkerProfile::
        where('location', 'near', [
            '$geometry' => [
                'type' => 'Point',
                'coordinates' => [
                     $longitude,$latitude
                ],
            ],
            '$maxDistance' => $distanc,
        ])
            ->where('fields' ,$category->name)
            ->where('availability_status', WorkerProfile::WORKER_AVAILABLE_STATUS)
            ->where('status',WorkerProfile::WORKER_ACCEPT_STATUS)
            ->where('has_active_order',false)->get(['user_id']);














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


            $clientUserModel = User::find($this->order->user_id);


                $clientUser['phone_number']=$clientUserModel->phone_number;
                $clientUser['name']=$clientUserModel->name;
                $clientUser['family']=$clientUserModel->family;

                $data=['order'=>$this->order,'user'=>$clientUser];




            $this->_sendNotifications($tokens,$data);







        }







    }

    private function _sendNotifications($tokens,$order)
    {
        $content = array(
            "en" => 'سفارش کار جدید'
        );
        $fields = array(
            'app_id' => "aae19cb0-6ce8-44e9-b40d-8038799b952e",
            'include_player_ids' => $tokens,
            'data' => $order,
            'contents' => $content
        );
        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic MzM4MjM1ZjktZDE1Ni00NDg2LWEyZWYtNDUxNDNlZmJmYWYy'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);

        curl_close($ch);




    }



}
