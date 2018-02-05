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

class SendSmsToUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $numbers;
    protected $message;

    /**
     * Create a new job instance.
     * @param $numbers
     * @param $message
     */
    public function __construct( array $numbers ,$message)
    {

        $this->numbers = $numbers;
        $this->message = $message;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {


            $sender = "100065995";
            $receptor = $this->numbers;
            $message = $this->message;
            $api = new \Kavenegar\KavenegarApi("41592B50794462786F746C68364338573231783474673D3D");
            $api->Send($sender, $receptor, $message);




        } catch(\Kavenegar\Exceptions\ApiException $e){

            // در صورتی که خروجی وب سرویس 200 نباشد این خطا رخ می دهد




        }
        catch(\Kavenegar\Exceptions\HttpException $e){
            // در زمانی که مشکلی در برقرای ارتباط با وب سرویس وجود داشته باشد این خطا رخ می دهد

        }



    }
}
