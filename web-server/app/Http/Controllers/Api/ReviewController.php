<?php

namespace App\Http\Controllers\Api;

use App\DissatisfiedReason;
use App\Order;
use App\OrderPayment;
use App\OrderStatusRevision;
use App\Review;
use App\Transaction;
use App\Wallet;
use App\WorkerProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;

class ReviewController extends Controller
{
    function review(Request $request)
    {

        $review = $request->getContent();


        $review = (json_decode($review));


        if (!property_exists($review, 'score')) return response()->json(['errors' => 'score is invalid'])->setStatusCode('417');


        if (!property_exists($review, 'order_id')) return response()->json(['errors' => 'order_id is invalid'])->setStatusCode('417');

        if (property_exists($review, 'reasons') and !is_array($review->reasons)) return response()->json(['errors' => 'review is invalid'])->setStatusCode('417');


        $order = Order::find($review->order_id);


        if (!$order) {
            return response()->json(['errors' => 'سفارسی با این نام وجود ندارد'])->setStatusCode('417');

        }

        if (!$order->worker_id)
            return response()->json(['errors' => 'هنوز خدمه برای این سفارش تعیین نشده'])->setStatusCode('417');




        if (property_exists($review,'reasons'))
        {

            $reasons=[];
            foreach ($review->reasons as $item)
                $reasons[] = new ObjectID($item);
            $review->reasons=$reasons;
        }





        $review->worker_id = new ObjectID($order->worker_id);
        $review->user_id = new ObjectID($request->user()->id);
        $review->order_id = new ObjectID($review->order_id);
        $review->created_at = new UTCDateTime(time() * 1000);

        $reviewModel = Review::where('worker_id', new ObjectID($order->worker_id))->orderBy('_id', 'desc')->first();




        if (!$reviewModel) {
            $review->mean_score = $review->score;
            $review->count = 1;


        } else {


            $review->mean_score = (($reviewModel->mean_score * $reviewModel->count) + $review->score) / ($reviewModel->count + 1);
            $review->count = $reviewModel->count + 1;
        }
        $workerProfile = WorkerProfile::where('user_id',new ObjectID($review->worker_id))->first();


        $workerProfile->mean_score=$review->mean_score;
        $workerProfile->save();
        $model = Review::raw()->insertOne($review);
        $review = Review::find((string)($model->getInsertedId()));

        return response()->json(['review', $review]);


    }


    function listReason()
    {
        $reasonModel = DissatisfiedReason::all();






        return response()->json(['reasons'=>$reasonModel]);
    }



}

