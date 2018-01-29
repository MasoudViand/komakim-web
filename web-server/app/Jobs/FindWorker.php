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

        $category=Category::find($category_id);


       // dd($category->name);


        $workersIds = WorkerProfile::
        where('location', 'near', [
            '$geometry' => [
                'type' => 'Point',
                'coordinates' => [
                    $latitude, $longitude
                ],
            ],
            '$maxDistance' => 5000,
        ])->
        where('fields' ,$category->name)->
        where('availability_status', 'available')->get(['user_id']);
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


            $this->_sendNotifications($tokens,$this->order);







        }else
        {

            $clientUserId=$this->order['user_id'];

            $clientUser=User::find($clientUserId);


            $clientToken=$clientUser['fcm_token'];

            $this->_sendFailFindWorkerNotificationToClient($clientToken);

            //Todo send data notification to user
        }







    }

    private function _sendNotifications($tokens,$order)
    {
        $content = array(
            "en" => 'dfgd'
        );
        $fields = array(
            'app_id' => "5cf31f6e-0526-4083-841b-03d789183ab8",
            'include_player_ids' => $tokens,
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




    }


}
