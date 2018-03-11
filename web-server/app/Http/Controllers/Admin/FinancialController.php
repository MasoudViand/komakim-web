<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\FinancialReport;
use App\Service;
use App\ServiceQuestion;
use App\Subcategory;
use App\User;
use App\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;
use function Symfony\Component\Debug\Tests\FatalErrorHandler\test_namespaced_function;

class FinancialController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:admin','financial'])->except(['filter']);
    }

    public function index(Request $request)
    {
        $mode ='daily';
        $queryParam =[];


        $fromDate =new \DateTime('midnight');
        $queryParam['from_date']=\Morilog\Jalali\jDateTime::strftime('Y/m/d', $fromDate);
        $fromDate =Carbon::instance($fromDate);
        $toDate=new \DateTime();
        $toDate = Carbon::instance($toDate);
        $queryParam['to_date']=\Morilog\Jalali\jDateTime::strftime('Y/m/d', $toDate);





        //from which date query should be executed

        if ($request->has('from_date'))
        {
            $queryParam['from_date']=$request->input('from_date');
            $date = \Morilog\Jalali\jDateTime::createDatetimeFromFormat('Y/m/d H:i:s', $request->input('from_date').' 00:00:00');

            $fromDate = Carbon::instance($date);

        }

        //until which date query should be executed

        if ($request->has('to_date'))
        {
            $queryParam['to_date']=$request->input('to_date');
            $date = \Morilog\Jalali\jDateTime::createDatetimeFromFormat('Y/m/d H:i:s', $request->input('to_date').' 00:00:00');

            $toDate = Carbon::instance($date);
        }

        $fromDate = new UTCDateTime($fromDate);
        $toDate = new UTCDateTime($toDate);
        $q_field=[
            [
                '$match' => [
                    'created_at' =>
                        [
                            '$gt' => $fromDate,
                            '$lt' => $toDate,
                        ]
                ]
            ]
        ];

        $q_field[]=
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
                    ],
                    'count' =>[
                        '$sum'=>1
                    ]
                ]

            ];

        $model = FinancialReport::raw()->aggregate($q_field);

        $reportArr=[];

        foreach ($model as $item)
        {
            array_push($reportArr,$item);
        }

        $commission_field=0;
        $total_price_field=0;
        $count_field =0;

        foreach ($reportArr as $item)
        {
            $commission_field= $commission_field+$item['commission'];
            $total_price_field    = $total_price_field +$item['total_price'];
            $count_field     = $count_field  +$item['count'];

        }

        $data['commission_field']=$commission_field;
        $data['total_price_field']=$total_price_field;
        $data['count_field']=$count_field;

        //////////////////////////////////////////



        if ($request->has('mode')){
            $mode =$request->input('mode');
        }

        $queryParam['mode']=$mode;


        $limit=20;
        if ($request->has('limit'))
        {
            $limit= (int)$request->input('limit');

            if ($limit>200 or $limit<=0)
                dd($limit);


        }

        $queryParam['limit']=$limit;
        $page =1;
        if ($request->has('page'))
            $page=(int)$request->input('page');
        $queryParam['page']=$page;


        if ($mode == 'daily')
        {
            if ($page==1)
                $to =new \DateTime();
            else
                $to =new \DateTime('-'.($limit*($page-1)-1).'days midnight');

            $from =new \DateTime('-'.($limit*($page)-1).'days midnight');


        }
        elseif ($mode == 'weekly')
        {
            $from =new \DateTime('-'.($limit*($page)-1).'weeks monday this week ');
            if ($page==1)
                $to =new \DateTime('-'.$limit*($page-1).'weeks');
            else
                $to =new \DateTime('-'.($limit*($page-1)-1).'weeks monday this week ');

        }
        else
        {
            $from =new \DateTime('-'.($limit*($page)-1).'months first day of this month');
            if ($page== 1)
                $to =new \DateTime('-'.$limit*($page-1).'months');
            else
                $to =new \DateTime('-'.($limit*($page-1)-1).'months first day of this month');


        }



        $from=Carbon::instance($from);
        $to  =Carbon::instance($to);


        $fromUtcFormt =new UTCDateTime($from);
        $toUtcFormat  = new UTCDateTime($to);


        $q=[
            [
                '$match' => [
                    'created_at' =>
                    [
                        '$gt' => $fromUtcFormt,
                        '$lt' => $toUtcFormat,
                    ]
                ]
            ]
        ];

        if ($mode=='daily'){
            $q[]=
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
                        ],
                        'count' =>[
                            '$sum'=>1
                        ]
                    ]

                ];


        }elseif ($mode=='weekly')
        {
            $q[]=
                [
                    '$group' =>[
                        '_id' =>[
                            'month' =>['$month'=>'$created_at'],
                            'week' =>['$week'=>'$created_at'],
                            'year' =>['$year'=>'$created_at'],

                        ],
                        'total_price' =>[
                            '$sum' =>'$total_price'
                        ],
                        'commission' =>[
                            '$sum' =>'$commission'
                        ],
                        'count' =>[
                            '$sum'=>1
                        ]
                    ]

                ];


        }else
        {
            $q[]=[

                    '$group' =>[
                        '_id' =>[
                            'month' =>['$month'=>'$created_at'],
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
            ];


        }


        $model = FinancialReport::raw()->aggregate($q);




        $reportArr=[];

        if ($mode=='daily')
        {
            foreach ($model as $item)
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
                array_push($reportArr,$item);
            }
        }elseif ($mode=='weekly')
        {

            foreach ($model as $item)
            {
                $week = $item['_id']['week'];
                if ($week<10)
                    $week ='0'.$week;
                $month = $item['_id']['month'];
                if ($month<10)
                    $month='0'.$month;
                $year =$item['_id']['year'];

                $date=$year.'/'.$month.'/'.$week;
                $item->date =$date;
                array_push($reportArr,$item);

            }

        }else
        {

            foreach ($model as $item)
            {
                $month = $item['_id']['month'];
                if ($month<10)
                    $month='0'.$month;
                $year =$item['_id']['year'];
                $date =$month.'/'.$year;
                $item->date =$date;
                array_push($reportArr,$item);

            }



        }


            $x_axis=[];
            $y_total_price=[];
            $y_commission=[];
            $y_count=[];

            if ($mode=='daily')
            {
                for ($i=0 ;$i<$limit ;$i++)
                {
                    $temDate=null;
                    $temDate=clone $to;
                    $temDate = $temDate->modify('-'.$i.' days');

                    $x_axis[]=$temDate->format('d/m/Y');
                }


                foreach ($x_axis as $item)
                {
                    $i=$this->find_key_value($reportArr ,'date',$item);

                    if ($this->find_key_value($reportArr ,'date',$item)!=='p')
                    {
                        $y_total_price[]=$reportArr[$i]['total_price'];
                        $y_commission[]=$reportArr[$i]['commission'];
                        $y_count  []    = $reportArr[$i]['count'];
                    }else
                    {
                        $y_total_price[]=null;
                        $y_commission[]=null;
                        $y_count[]       =null;
                    }


                }
            }elseif ($mode=='weekly')
            {
                for ($i=0 ;$i<$limit ;$i++)
                {
                    $temDate=null;
                    $temDate=clone $to;
                    $temDate = $temDate->modify('-'.$i.' weeks');
                    $temDate->format('d/m/Y');
                    //$x_axis[]=$temDate->format("Y").'/'.$temDate->format("m").'/'.$temDate->format("W");
                }
                foreach ($x_axis as $x_axi)
                {
                    $i=$this->find_key_value($reportArr ,'date',$x_axi);



                    if ($this->find_key_value($reportArr ,'date',$x_axi)!=='p')
                    {
                        $y_total_price[]=$reportArr[$i]['total_price'];
                        $y_commission[]=$reportArr[$i]['commission'];
                        $y_count  []    = $reportArr[$i]['count'];
                    }else
                    {
                        $y_total_price[]=null;
                        $y_commission[]=null;
                        $y_count[]       =null;
                    }
                }
            }else
            {
                for ($i=0 ;$i<$limit ;$i++)
                {
                    $temDate=null;
                    $temDate=clone $to;
                    $temDate = $temDate->modify('-'.$i.' months');
                    $x_axis[]=$temDate->format('m/Y');
                }

                foreach ($x_axis as $x_axi)
                {
                    $i=$this->find_key_value($reportArr ,'date',$x_axi);

                    if ($this->find_key_value($reportArr ,'date',$x_axi)!=='p')
                    {
                        $y_total_price[]=$reportArr[$i]['total_price'];
                        $y_commission[]=$reportArr[$i]['commission'];
                        $y_count  []    = $reportArr[$i]['count'];
                    }else
                    {
                        $y_total_price[]=null;
                        $y_commission[]=null;
                        $y_count[]       =null;
                    }
                }



            }



        $total_price_sum= array_filter($y_total_price);
        $total_price_sum=array_sum($total_price_sum);
        $commission_sum=array_filter($y_commission);
        $commission_sum=array_sum($commission_sum);
        $count_sum     = array_filter($y_count);
        $count_sum     = array_sum($count_sum);






        $data['x_axis']=            $x_axis;
        $data['y_total_price']=     $y_total_price;
        $data['total_price_sum']=   $total_price_sum;
        $data['y_commission']=      $y_commission;
        $data['commission_sum']=    $commission_sum;
        $data['y_count']=           $y_count;
        $data['count_sum']=         $count_sum;
        $data['queryparam']  =      $queryParam;
        $preQueryparam =            $queryParam;
        $nextQueryparam =           $queryParam;
        $preQueryparam['page']=     $page-1;
        $nextQueryparam['page']=    $page+1;
        $data['preQueryparam']  =   $preQueryparam;
        $data['nextQueryparam']  =  $nextQueryparam;
        $data['page_title']=        'گزارش مالی';



        return view('admin.pages.financial.index')->with($data);
    }
    public function filter( Request $request )
    {

        $content = $request->getContent();

        $content =(json_decode($content));

        $from =new \DateTime('- 6 months');
        $to   = new \DateTime();


        if (isset($content->from))
        {

            $requestFrom = \Morilog\Jalali\jDateTime::createDatetimeFromFormat('Y/m/d H:i:s', $content->from.' 00:00:00');
            if ($requestFrom<$from)
            {
                dd('wrong datetime');
            }
            $from=$requestFrom;


        }

        if (isset($content->to))
        {
            $requestTo = \Morilog\Jalali\jDateTime::createDatetimeFromFormat('Y/m/d H:i:s', $content->to.' 00:00:00');

            if ($requestTo>$to)
                dd('wrong gto time');
            $to = $requestTo;
        }

        if ($to< $from)
        {
            dd(11);
        }

        $from = Carbon::instance($from);
        $to   =Carbon::instance($to);

        $from = new UTCDateTime($from);

        $to =   new  UTCDateTime($to);

        $q=[
            [
                '$match' => [
                    'created_at' =>
                        [
                            '$gt' => $from,
                            '$lt' => $to,
                        ]
                ]
            ]
        ];


        $mode =$content->mode;


        if ($mode=='daily'){
            $q[]=
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
                        ],
                        'count' =>[
                            '$sum'=>1
                        ]
                    ]

                ];


        }elseif ($mode=='weekly')
        {
            $q[]=
                [
                    '$group' =>[
                        '_id' =>[
                            'month' =>['$month'=>'$created_at'],
                            'week' =>['$week'=>'$created_at'],
                            'year' =>['$year'=>'$created_at'],

                        ],
                        'total_price' =>[
                            '$sum' =>'$total_price'
                        ],
                        'commission' =>[
                            '$sum' =>'$commission'
                        ],
                        'count' =>[
                            '$sum'=>1
                        ]
                    ]

                ];


        }else
        {
            $q[]=[

                '$group' =>[
                    '_id' =>[
                        'month' =>['$month'=>'$created_at'],
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
            ];


        }

        $model = FinancialReport::raw()->aggregate($q);

        $reportArr=[];

        foreach ($model as $item)
        {
            array_push($reportArr,$item);
        }

        $total_commission=0;
        $total_prices=0;
        $total_count =0;

        foreach ($reportArr as $item)
        {
            $total_commission= $total_commission+$item['commission'];
            $total_prices    = $total_prices +$item['total_price'];
            $total_count     = $total_count  +$item['count'];

        }

        $data['total_commission']=$total_commission;
        $data['total_prices']=$total_prices;
        $data['total_count']=$total_count;


        return response()->json($data);





    }




    function showRemainWallet()
    {

        $q = [


            [ '$lookup' => [
                'from'         => 'users',
                'localField'   => 'user_id',
                'foreignField' => '_id',
                'as'           => 'user',],

            ],
            [
                '$match' => [
                    'amount'   =>[
                        '$gt' =>0
                    ],
                    'user.role' => User::CLIENT_ROLE,
                ]            ],
            [
                '$group' =>[
                    '_id' =>null,
                    'total_amount' =>[
                        '$sum' =>'$amount'
                    ]
                ]
            ],

        ];

        $model = Wallet::raw()->aggregate($q);
        $wallerArr=[];
        foreach ($model as $item)
        {
            array_push($wallerArr,$item);
        }
        $amount =0;


        if (count($wallerArr)>0)
            $amount = ($wallerArr[0]['total_amount']);


        $data['amount']=$amount;
        $data['page_title']='مقادیر کیف پول استفاده نشده';


        return view('admin.pages.financial.remain')->with($data);
    }


    function find_key_value($array, $key, $val)
    {
        $i =0;
        foreach ($array as $item)
        {
            if (is_array($item) && find_key_value($item, $key, $val)) return $i;

            if (isset($item[$key]) && $item[$key] == $val) return $i;
            $i++;
        }

        return 'p';
    }


}
