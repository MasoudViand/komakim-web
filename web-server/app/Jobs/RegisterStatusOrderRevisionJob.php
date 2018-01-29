<?php

namespace App\Jobs;

use App\OrderStatusRevision;
use App\User;
use Faker\Provider\DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;

class RegisterStatusOrderRevisionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $user;
    protected $status;
    protected $order_id;

    /**
     * Create a new job instance.
     * @param User $user
     * @param $status
     * @param  $order_id
     *
     * @return void
     */
    public function __construct( $order_id,  $status, $user)
    {
        $this->user     =$user;
        $this->status   =$status;
        $this->order_id  =$order_id;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $revision = new \stdClass();

        $revision->order_id=($this->order_id);
        $revision->created_at = new UTCDateTime(time()*1000);
        $revision->status=$this->status;
        $revision->whom =new ObjectID($this->user->id);

        $model = OrderStatusRevision::raw()->insertOne($revision);
    }
}
