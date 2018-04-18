<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\FinancialReport;
use App\Service;
use App\ServiceQuestion;
use App\Subcategory;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Faker\Provider\DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;
use function PHPSTORM_META\type;
use function Symfony\Component\Debug\Tests\FatalErrorHandler\test_namespaced_function;

class TransactionsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:admin','financial'])->except(['filterDaily','filterMonthly','filterWeekly']);
    }

     function index(Request $request)
    {
        $queryParam =[];

        //how many record should show to user

        if ($request->has('limit'))
        {
            $limit =(int)$request->input('limit');

        }
        else
            $limit=20;
        $queryParam['limit']=$limit;


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


        $transactionsQuery = new Transaction();
        $transactionsQuery = $transactionsQuery->newQuery();

        //if request has query param type should be added to query


        if ($request->has('type'))
        {
            $transactionsQuery->where('type',$request->input('type'));
            $queryParam['type']=$request->input('type');

        }

        //add datetime expression to query builder

        $transactionsQuery->whereBetween('created_at',[$from,$to]);

        //get count off all record that match with this query (this count use for calculate total page)

        $transactionsCount =$transactionsQuery->count();


        //execute query with skip and limit that retrieve by query param

         $transactionsModel =$transactionsQuery->orderBy('_id','desc')->skip($skip)->take($limit)->get();




        $transactions=[];
        foreach ($transactionsModel as $item)
        {



            $transaction=[];

            switch ($item['type'])
            {
                case Transaction::PAY_ORDER:
                    $transaction['type'] ='پرداحت مشتری';
                    break;
                case Transaction::DONE_ORDER:
                    $transaction['type'] = 'واریز به کیف پول خدمه ';
                    break;
                case Transaction::CHARGE_FROM_BANK:
                    $transaction['type'] ='شارژ کیف پول مشتری';
                    break;
                case Transaction::BALANCE_ACCOUNT:
                    $transaction['type']='تسفیه حساب خدمه';
                    break;
                default:
                    $transaction['type'] = 'تراکنش نا شناخته';
                    break;
            }

            $transaction['amount']=$item['amount'];
            $transaction['created_at'] = \Morilog\Jalali\jDateTime::strftime('y/m/d H:i:s', strtotime($item['created_at']));
            $transaction['user'] = User::find($item['user_id']);
            $transactions[]=$transaction;


        }

        $data['transactions']=$transactions;
        $data['total_count']=$transactionsCount;
        $data['total_page']=((int)($data['total_count']/$limit))+1;
        $data['queryParam']=$queryParam;
        $data['page_title']='تراکنش ها';





        return view('admin.pages.transactions.index')->with($data);
    }

    function export(Request $request)
    {
        if ($request->has('limit')){
            $limit =(int)$request->input('limit');
        }
        else
            $limit =10;
         if ($request->has('page'))
         {
             $skip = (int)($request->input('page')-1)*$limit;
         }
         else
             $skip =0;

        $from =new \DateTime('-5 years');
        $from =Carbon::instance($from);
        $to = Carbon::now();

        //from which date query should be executed

        if ($request->has('from'))
        {

            $date = \Morilog\Jalali\jDateTime::createDatetimeFromFormat('Y/m/d H:i:s', $request->input('from').' 00:00:00');

            $from = Carbon::instance($date);
        }

        //until which date query should be executed

        if ($request->has('to'))
        {
            $date = \Morilog\Jalali\jDateTime::createDatetimeFromFormat('Y/m/d H:i:s', $request->input('to').' 00:00:00');


            $to = Carbon::instance($date);
        }

        $from = new UTCDateTime($from);
        $to = new UTCDateTime($to);



        $transactionsQuery = new Transaction();
        $transactionsQuery = $transactionsQuery->newQuery();

        //if request has query param type should be added to query


        if ($request->has('type'))
        {
            $transactionsQuery->where('type',$request->input('type'));

        }

        //add datetime expression to query builder

        $transactionsQuery->whereBetween('created_at',[$from,$to]);

        //get count off all record that match with this query (this count use for calculate total page)



        //execute query with skip and limit that retrieve by query param

        $transactionsModel =$transactionsQuery->orderBy('_id','desc')->skip($skip)->take($limit)->get();

        // dd($walletArr[0]['user'][0]['name']);
        $scv_total =[];

        $scv_row=['مقدار ','نام خانوادگی','تاریخ','نوع'];
        $scv_total[]=$scv_row;
        $scv_row=[];

        $transactionsArr =[];
        foreach ($transactionsModel as $item)
        {
            array_push($transactionsArr,$item);
        }

        foreach ($transactionsArr as $item)
        {
            $transaction=[];

            switch ($item['type'])
            {
                case Transaction::PAY_ORDER:
                    $transaction['type'] ='پرداحت مشتری';
                    break;
                case Transaction::DONE_ORDER:
                    $transaction['type'] = 'واریز به کیف پول مشتری ';
                    break;
                case Transaction::CHARGE_FROM_BANK:
                    $transaction['type'] ='شارژ کیف پول مشتری';
                    break;
                case Transaction::BALANCE_ACCOUNT:
                    $transaction['type']='تسفیه حساب مشتری';
                    break;
                default:
                    $transaction['type'] = 'تراکنش نا شناخته';
                    break;
            }

            $transaction['amount']=$item['amount'];
            $transaction['created_at'] = \Morilog\Jalali\jDateTime::strftime('y/m/d H:i:s', strtotime($item['created_at']));
            $transaction['user'] = User::find($item['user_id']);


           // dd($transaction);

            $scv_row[]=$transaction['amount'];
            $scv_row[]=$transaction['user']['name'].' '.$transaction['user']['family'];
            $scv_row[]=$transaction['created_at'];
            $scv_row[]=$transaction['type'];
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

      //  fputcsv($file, ['نام ','نام خانوادگی','موبایل','اعتبار']);
        foreach ($scv_total as $item)
        {
            fputcsv($file, $item);

        }

        exit();


    }




}
