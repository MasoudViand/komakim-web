<?php

namespace App\Http\Controllers\Admin;

use App\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use MongoDB\BSON\UTCDateTime;

class SurveyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:admin','operator']);
    }

    public function index(Request $request)
    {

        $queryParam =[];


        $limit=10;
        $queryParam['limit']=$limit;


        //how many record should show to user





        //which page should show to user

        if ($request->has('page'))
        {
            $skip=($request->input('page')-1)*$limit;
            $queryParam['page']=$request->input('page');

        }
        else
            $skip =0;

        $from =new \DateTime('-5 years');
        $from =Carbon::instance($from);
        $to = Carbon::now();

        //from which date query should be executed

        if ($request->has('from'))
        {
            $queryParam['from']=$request->input('from');
            $date = \Morilog\Jalali\jDateTime::createDatetimeFromFormat('Y/m/d H:i:s', $request->input('from').' 00:00:00');

            $from = Carbon::instance($date);

        }

        //until which date query should be executed

        if ($request->has('to'))
        {
            $queryParam['to']=$request->input('to');
            $date = \Morilog\Jalali\jDateTime::createDatetimeFromFormat('Y/m/d H:i:s', $request->input('to').' 00:00:00');

            $to = Carbon::instance($date);
        }

        $from = new UTCDateTime($from);
        $to = new UTCDateTime($to);


        $reviewsQuery = new Review();
        $reviewsQuery = $reviewsQuery->newQuery();

        //if request has query param type should be added to query


        $q = [
            ['$sort'=>['_id'=>-1]],
            [ '$skip' => $skip ],
            [ '$limit' => $limit ],

            [ '$lookup' => [
                'from'         => 'users',
                'localField'   => 'user_id',
                'foreignField' => '_id',
                'as'           => 'client',],

            ],
            [ '$lookup' => [
                'from'         => 'users',
                'localField'   => 'worker_id',
                'foreignField' => '_id',
                'as'           => 'worker',],

            ],
            [ '$lookup' => [
                'from'         => 'dissatisfied_reasons',
                'localField'   => 'reasons',
                'foreignField' => '_id',
                'as'           => 'reasons_field',],

            ],

            [
                '$match'=>[
                    'created_at' =>
                        [
                            '$gt' => $from,
                            '$lt' => $to,
                        ]
                ]
            ],
        ];
        $q_count= [
            [
                '$match'=>[
                    'created_at' =>
                        [
                            '$gt' => $from,
                            '$lt' => $to,
                        ]
                ]
            ],
            [
                '$count'=>'count'
            ]


        ];

        if ($request->has('sort'))
        {
            $queryParam['sort']=$request->input('sort');
            array_shift($q);
            if ($request->input('sort')=='desc')
            {
                $ql=[
                    '$sort' =>[
                        'score' =>1
                    ]
                ];
                array_unshift($q,$ql);
            }elseif($request->input('sort')=='asc')
            {
                $ql=[
                    '$sort' =>[
                        'score' =>-1
                    ]
                ];
                array_unshift($q,$ql);
            }

        }






        $model = Review::raw()->aggregate($q);
        $count = Review::raw()->aggregate($q_count);

        $review_count=0;

        foreach ($count as $item)
            $review_count=$item['count'];

        $reviews=[];


        foreach ($model as  $item)
        {

            $item['created_at_hour']=\Morilog\Jalali\jDateTime::strftime('H:m', $item['created_at']->toDateTime());
            $item['created_at'] = \Morilog\Jalali\jDateTime::strftime('d/m/Y', $item['created_at']->toDateTime());

           // $item->created_at=($item->created_at)->toDateTime();
            $reviews[]=$item;
        }



        $data['reviews']=$reviews;
        $data['queryParam']=$queryParam;
        $data['page_title']='لیست نظر سنجی';

        $data['total_page']=(int)($review_count/$limit)+1;

        return view('admin.pages.reviews.index')->with($data);

    }
}
