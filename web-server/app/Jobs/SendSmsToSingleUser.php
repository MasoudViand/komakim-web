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
class SendSmsToSingleUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $number;
    protected $message;

    /**
     * Create a new job instance.
     *
     * @param string $number
     * @param string $message
     *
     * @return void
     */
    public function __construct(  $number,$message)
    {
        $this->message=$message;
        $this->number=$number;
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
            $receptor = $this->number;
            $message = $this->message;
            $api = new \Kavenegar\KavenegarApi("41592B50794462786F746C68364338573231783474673D3D");
            $api->Send($sender, $receptor, $message);



        } catch(\Kavenegar\Exceptions\ApiException $e){



        }
        catch(\Kavenegar\Exceptions\HttpException $e){
            // در زمانی که مشکلی در برقرای ارتباط با وب سرویس وجود داشته باشد این خطا رخ می دهد

        }

    }


}
