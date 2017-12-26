<?php

namespace App\Jobs;

use App\Order;
use App\User;
use App\WorkerProfile;
use function GuzzleHttp\Psr7\str;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
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
    public function __construct( stdClass $order)
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

        $category_id =$this->order->category_id;

        $latitude =$this->order->address->latitude;
        $longitude =$this->order->address->longitude;

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

        reset($workersIds);
        $key = key($workersIds);

     //   dd($key);
        unset($workersIds[$key]);


        dd($workersIds);



        if ($workersIds)
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
            dd($tokens);

            //Todo send notifaction to workers

            //Todo run worker accetaption job /

        }else
        {
            //Todo send data notification to user
        }




        Log::info($this->order->address->plain_text);


    }
}
