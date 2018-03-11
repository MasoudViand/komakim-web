<?php

namespace App\Http\Controllers\Admin;

use App\DissatisfiedReason;
use App\Review;
use App\Transaction;
use App\User;
use App\Wallet;
use App\WorkerProfile;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Excel;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;


class SettleDeptController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin','financial'])->except(['settleWorker','export']);
    }

    function index(Request $request){


        if ($request->has('limit'))
        {
                $limit=(int)$request->input('limit');

        }
        else
            $limit=20;

        if ($request->has('page'))
        {
            $skip=($request->input('page')-1)*$limit;
            $queryParam['page']=(int)$request->input('page');

        }
        else
        {
            $skip=0;
            $queryParam['page']=1;
        }


        $queryParam['limit']=$limit;

        $queryParam['skip']=$skip;

        $q = [
            ['$sort'=>['_id'=>-1]],
            [ '$limit' => $limit ],
            [ '$skip'  => $skip   ],



            [ '$lookup' => [
                'from'         => 'users',
                'localField'   => 'user_id',
                'foreignField' => '_id',
                'as'           => 'user',],

            ],
            [ '$lookup' => [
                'from'         => 'worker_profiles',
                'localField'   => 'user_id',
                'foreignField' => 'user_id',
                'as'           => 'worker_profile',],

            ],

            [
                '$match' => [
                    'amount'   =>[
                        '$gt' =>0
                    ],
                    'user.role' => User::WORKER_ROLE,
                ]            ],

        ];
        $q_count=[


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
                    'user.role' => User::WORKER_ROLE,
                ]         ],
            [
                '$count'=>'count'
            ]

        ];





        $model = Wallet::raw()->aggregate($q);
        $count = Wallet::raw()->aggregate($q_count);
        $walletArr=[];
        foreach ($model as $item)
        {
            array_push($walletArr,$item);
        }





        $countArr=[];

        foreach ($count as $value)
            array_push($countArr,$value);




        $data['wallets']=$walletArr;
        if (count($countArr)>0)
            $data['count']=$countArr[0]['count'];
        else
            $data['count']=0;



        $data['total_page']=(int)($data['count']/10)+1;
        $data['queryParam']=$queryParam;
        $data['page_title']='تسویه حساب';

        $total_worker_amount_wallet=$this->_calculateTotalWorkerRemailWallet();
        $data['total_worker_amount_wallet']=$total_worker_amount_wallet;




        return view('admin.pages.settle.index')->with($data);
    }


    function settleWorker(Request $request)
    {

       $content = $request->getContent();

       $content=json_decode($content);


       $wallets =Wallet::find($content);


       foreach ($wallets as $wallet)
       {
           $amount=$wallet->amount;
           $wallet->amount =0 ;
           if ($wallet->save())
           {
               $settleTransaction = new \stdClass();

               $settleTransaction->amount= $amount;
               $settleTransaction->user_id =$wallet->user_id;
               $settleTransaction->created_at =new UTCDateTime(time()*1000);
               $settleTransaction->type = Transaction::BALANCE_ACCOUNT;
               $meta = new \stdClass();
               $meta->type= Transaction::BALANCE_ACCOUNT;

               $model = Transaction::raw()->insertOne($settleTransaction);
           }
       }

       return response()->json(['wallets'=>$wallets]);

    }
    function export(Request $request)
    {
        $skip=(int)$request->input('skip');
        $limit =(int)$request->input('limit');


        $q = [
            ['$sort'=>['_id'=>-1]],
            [ '$skip'=> $skip],
            [ '$limit' => $limit ],
            [ '$lookup' => [
                'from'         => 'users',
                'localField'   => 'user_id',
                'foreignField' => '_id',
                'as'           => 'user',],

            ],
            [ '$lookup' => [
                'from'         => 'worker_profiles',
                'localField'   => 'user_id',
                'foreignField' => 'user_id',
                'as'           => 'worker_profile',],

            ],
            [
                '$match' => [
                    'amount'   =>[
                        '$gt' =>0
                    ],
                    'user.role' => User::WORKER_ROLE,
                ]            ]


        ];



        $model = Wallet::raw()->aggregate($q);
        $walletArr=[];
        foreach ($model as $item)
        {

            array_push($walletArr,$item);
        }

            $scv_total =[];

            $scv_row=['نام ','نام خانوادگی','موبایل','شماره شبا','اعتبار'];
            $scv_total[]=$scv_row;
            $scv_row=[];

        foreach ($walletArr as $wallet)
        {
            $scv_row[]=$wallet['user'][0]['name'];
            $scv_row[]=$wallet['user'][0]['family'];
            $scv_row[]=$wallet['user'][0]['phone_number'];
            $scv_row[]=key_exists('account_number',$wallet['worker_profile'][0])?$wallet['worker_profile'][0]['account_number']:'تعیین نشده';
            $scv_row[]=$wallet['amount'];
            $scv_total[]=$scv_row;
            $scv_row=[];

        }

        $file = fopen('php://output', 'w');


        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=export.csv');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        $file = fopen('php://output', 'w');

        fputcsv($file, ['نام ','نام خانوادگی','موبایل','شماره شبا','اعتبار']);
        foreach ($walletArr as $wallet)
        {
            $scv_row[]=$wallet['user'][0]['name'];
            $scv_row[]=$wallet['user'][0]['family'];
            $scv_row[]=$wallet['user'][0]['phone_number'];
            $scv_row[]=key_exists('account_number',$wallet['worker_profile'][0])?$wallet['worker_profile'][0]['account_number']:'تعیین نشده';
            $scv_row[]=$wallet['amount'];
            fputcsv($file, $scv_row);
            $scv_row=[];

        }

        exit();



    }


    private function _calculateTotalWorkerRemailWallet()
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
                    'user.role' => User::WORKER_ROLE,
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


        return $amount;
    }






}
