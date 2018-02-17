<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\FinancialReport;
use App\Http\Controllers\Controller;
use App\Order;
use App\Subcategory;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use MongoDB\BSON\UTCDateTime;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $to =new \DateTime();
        $from =new \DateTime('-'.(1).'days midnight');


        $from=Carbon::instance($from);
        $to  =Carbon::instance($to);


        $fromUtcFormt =new UTCDateTime($from);
        $toUtcFormat  = new UTCDateTime($to);

        $q_count=
            [
                [
                    '$match' => [
                        'created_at' =>
                            [
                                '$gt' => $fromUtcFormt,
                                '$lt' => $toUtcFormat,
                            ]
                    ]
                ],
                [
                    '$group' =>[
                        '_id' =>[
                            'month' =>['$month'=>'$created_at'],
                            'day' =>['$dayOfMonth'=>'$created_at'],
                            'year' =>['$year'=>'$created_at'],

                        ],

                        'count' =>[
                            '$sum'=>1
                        ]
                    ]

                ]

            ];

        $q_total_price=
            [
                [
                    '$match' => [
                        'created_at' =>
                            [
                                '$gt' => $fromUtcFormt,
                                '$lt' => $toUtcFormat,
                            ]
                    ]
                ],
                [
                    '$group' =>[
                        '_id' =>[
                            'month' =>['$month'=>'$created_at'],
                            'day' =>['$dayOfMonth'=>'$created_at'],
                            'year' =>['$year'=>'$created_at'],

                        ],
                        'total_price' =>[
                            '$sum' =>'$total_price'
                        ],
                        'commission' =>[
                            '$sum' =>'$commission'
                        ]
                        ,

                        'count' =>[
                            '$sum'=>1
                        ]
                    ]

                ]

            ];


        $financialModel = FinancialReport::raw()->aggregate($q_total_price);
        $orderModel = Order::raw()->aggregate($q_count);
        $userModel = User::raw()->aggregate($q_count);


        $userArr=[];

        foreach ($userModel as $item)
        {
            array_push($userArr,$item);
        }
        $financialArr=[];

        foreach ($financialModel as $item)
        {
            array_push($financialArr,$item);
        }


        $orderArr=[];

        foreach ($orderModel as $item)
        {
            array_push($orderArr,$item);
        }

        $orders=[];
        foreach ($orderArr as $item)
        {

            $day= $item['_id']['day'];
            if ($day<10)
                $day='0'.$day;
            $month = $item['_id']['month'];
            if ($month<10)
                $month='0'.$month;
            $year = $item['_id']['year'];
            $date=$day.'/'.$month.'/'.$year;
            $item->date=$date;

            if ($date==$to->format('d/m/Y'))
            {
                $orders['today']=$item->count;
                $item->type ='today';
            }
            if ($date== $from->format('d/m/Y'))
                $orders['yesterday']=$item->count;
                $item->type= 'yesterday';

        }
        $users=[];
        foreach ($userArr as $item)
        {
            $day= $item['_id']['day'];
            if ($day<10)
                $day='0'.$day;
            $month = $item['_id']['month'];
            if ($month<10)
                $month='0'.$month;
            $year = $item['_id']['year'];
            $date=$day.'/'.$month.'/'.$year;
            $item->date=$date;
            if ($date==$to->format('d/m/Y'))
            {
                $users['today']=$item->count;
                $item->type ='today';
            }
            if ($date== $from->format('d/m/Y'))
                    $users['yesterday']=$item->count;

            $item->type= 'yesterday';
        }
        $financials=[];
        foreach ($financialArr as $item)
        {
            $day= $item['_id']['day'];
            if ($day<10)
                $day='0'.$day;
            $month = $item['_id']['month'];
            if ($month<10)
                $month='0'.$month;
            $year = $item['_id']['year'];
            $date=$day.'/'.$month.'/'.$year;
            $item->date=$date;
            if ($date==$to->format('d/m/Y'))
            {
                $financials['today']=$item->total_price;
                $item->type ='today';
            }
            if ($date== $from->format('d/m/Y'))
                $financials['yesterday']=$item->total_price;
            //$item->type= 'yesterday';
        }




        $q['financials']=$financials;
        $q['orders']=$orders;
        $q['users']=$users;
        $q['page_title']='داشبورد';




        return view('admin.pages.dashboard')->with($q);
    }

    function showChangePassForm()
    {
        return view('admin.pages.change_pass');
    }
    function changePass(Request $request)
    {
        $this->validate($request, [
            'currentPassword' =>'required',
            'password' => 'required|string|min:6|confirmed',]);


        $user =$request->user();

        if(Hash::check($request->input('currentPassword'), $user->password))
        {
            $user->password =bcrypt($request->input('password'));
            $user->save();
            $message['success']='گذر واژه با موفقیت تغییر یافت';
        }
        else{
            $message['error']='گذر واژه فعلی اشتباه میباشد';
        }
        return redirect()->route('admin.dashboard')->with($message);

    }

}
