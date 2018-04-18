<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\DiscountCode;
use App\Subcategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use MongoDB\BSON\UTCDateTime;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;

class DiscountCodeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:admin','admin']);
    }

    public function index(){


        $discountCodes =DiscountCode::orderBy('_id','desc')->paginate(20);

        foreach ($discountCodes as $item)
        {
            if ($item->expired_at!='unlimited')
                $item->expired_at = \Morilog\Jalali\jDateTime::strftime('Y/m/d', $item['expired_at']->toDateTime());
            else
                $item->expired_at ='نامحدود';


        }


        $data['discountCodes']=$discountCodes;
        $data['total_count']=DiscountCode::count();
        $data['page_title']='کد تخفیف';


        return view('admin.pages.discount_code.listDiscount_code')->with($data);;

    }
    public function insertForm()
    {
        $fields =Category::all();
        $data['fields']=$fields;
        return view('admin.pages.discount_code.addDiscount_code')->with($data);

    }
     function insert(Request $request)
    {

        $this->validate($request,[
            'discount_code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
        ]);


        if (DiscountCode::where('name',$request['discount_code'])->first())
        {
            $message['error'] = 'این کد تحفیف قبلا درج شده است';
            return redirect()->back()->with($message);
        }

        if (!is_null($request->input('expired_at')))
        {
            $date = \Morilog\Jalali\jDateTime::createDatetimeFromFormat('Y/m/d H:i:s', $request->input('expired_at').' 00:00:00');

            if ($date<new \DateTime())
                return redirect()->back()->with(['error'=>'تاریخ انقضا نمی تواند از تاریخ گنونی کمتر باشد']);
        }



        $category = new DiscountCode();
        if (!is_null($request->input('total_use_limit')))
            $category->total_use_limit = (int)$request->input('total_use_limit');
        else
            $category->total_use_limit = 'unlimited';

         if (!is_null($request->input('expired_at')))
        {
            $date = \Morilog\Jalali\jDateTime::createDatetimeFromFormat('Y/m/d H:i:s', $request->input('expired_at').' 00:00:00');
            $date = Carbon::instance($date);
            $date = new UTCDateTime($date);
            $category->expired_at=$date;
            $category->count_of_used=0;

        }
        else
            $category->expired_at='unlimited';

         if (!is_null($request->input('fields')))
         {
            $category->fields = $request->input('fields');
         }else
             $category->fileds='unlimited';
         if (!is_null($request->input('upper_limit_use')))
             $category->upper_limit_use=(int)$request->input('upper_limit_use');
         else
             $category->upper_limit_use='unlimited';
         if (!is_null($request->input('user_limit')))
             $category->user_limit=(int)$request->input('user_limit');
         else
             $category->user_limit = 'unlimited';


         $category->name = $request['discount_code'];
        $category->type = $request['type'] ;
        $category->value = (int)$request['value'];
        $category->status = true;

        if ($category->save())
            $message['success'] = 'کد تخفیف با موفقیت اضافه شد ';
        else
            $message['error'] = 'مجددا تلاش کنید ';

        return redirect()->back()->with($message);


    }


    function updateForm($discount_id)
    {
        $discount = DiscountCode::find($discount_id);



        if ($discount->expired_at!='unlimited')
            $discount->expired_at = \Morilog\Jalali\jDateTime::strftime('Y/m/d', $discount['expired_at']->toDateTime());


        $fields =Category::all();
        $data['fields']=$fields;
        $data['discount']=$discount;
        return view('admin.pages.discount_code.updateDiscount_code')->with($data);

    }
    function update(Request $request)
    {



        $this->validate($request,[
            'type' => 'required',
            'value' => 'required|numeric',
        ]);


        if (!is_null($request->input('expired_at')))
        {
            $date = \Morilog\Jalali\jDateTime::createDatetimeFromFormat('Y/m/d H:i:s', $request->input('expired_at').' 00:00:00');

            if ($date<new \DateTime())
                return redirect()->back()->with(['error'=>'تاریخ انقضا نمی تواند از تاریخ گنونی کمتر باشد']);
        }

      //  dd($request->input('status'));




        $category = DiscountCode::find($request->input('discountId'));

        if ($request->input('status')=='true')
            $category->status=true;
        else
            $category->status = false;
        if (!is_null($request->input('total_use_limit')))
            $category->total_use_limit = (int)$request->input('total_use_limit');
        else
            $category->total_use_limit = 'unlimited';

        if (!is_null($request->input('expired_at')))
        {
            $date = \Morilog\Jalali\jDateTime::createDatetimeFromFormat('Y/m/d H:i:s', $request->input('expired_at').' 00:00:00');
            $date = Carbon::instance($date);
            $date = new UTCDateTime($date);
            $category->expired_at=$date;

        }
        else
            $category->expired_at='unlimited';

        if (!is_null($request->input('fields')))
        {
            $category->fields = $request->input('fields');
        }else
            $category->fields='unlimited';
        if (!is_null($request->input('upper_limit_use')))
            $category->upper_limit_use=(int)$request->input('upper_limit_use');
        else
            $category->upper_limit_use='unlimited';
        if (!is_null($request->input('user_limit')))
            $category->user_limit=(int)$request->input('user_limit');
        else
            $category->user_limit = 'unlimited';


        $category->type = $request['type'] ;
        $category->value = (int)$request['value'];

        if ($category->save())
            $message['success'] = 'کد تخفیف با موفقیت ویرای شد ';
        else
            $message['error'] = 'مجددا تلاش کنید ';

        return redirect()->back()->with($message);

    }
}
