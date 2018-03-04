<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\DiscountCode;
use App\Subcategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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


        $discountCodes =DiscountCode::orderBy('_id','desc')->paginate(15);
        $data['discountCodes']=$discountCodes;
        $data['total_count']=DiscountCode::count();
        $data['page_title']='کد تخفیف';


        return view('admin.pages.discount_code.listDiscount_code')->with($data);;

    }
    public function insertForm()
    {

        return view('admin.pages.discount_code.addDiscount_code');

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

        $category = new DiscountCode();
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

     function inactive ($discount_code_id)
    {
        $discount_code= DiscountCode::find($discount_code_id);
        if ($discount_code)
        {
            $discount_code->status=false;
            if ($discount_code->save())
                $message['success'] = 'کد تخفیف با با موفقیت غیر فعال شد ';
            else
                $message['error'] = 'مجددا تلاش کنید ';

        }else
            $message['success'] = 'کد تخفیف وجود ندارد';


        return redirect()->back()->with($message);
    }
}
