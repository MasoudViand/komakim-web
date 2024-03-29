<?php

namespace App\Http\Controllers\Admin;

use App\CancelReason;
use App\DissatisfiedReason;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use function PHPSTORM_META\type;

class CancelReasonController extends Controller
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

    function index()
    {

        $cancelReasons = CancelReason::orderBy('_id','desc')->get();
        foreach ($cancelReasons as $item)
        {
            $item->type=$item->type==User::WORKER_ROLE ?'خدمه':'مشتری';

        }

        $data['cancelReasons'] = $cancelReasons;
        $data['page_title']='دلایل لغو سفارش';

        return view('admin.pages.cancel_reason.list_cancel_reason')->with($data);


    }

    function addCancelReasonForm(){

        $data['page_title']='افزودن دلیل لغو سفارش';

        return view('admin.pages.cancel_reason.addCancelReason')->with($data);

    }

    function addCancelReason(Request $request)
    {

        $this->validate($request,[
            'CancelReason' => 'required',
            'type' => 'required',
        ]);

        $cancelReason = new CancelReason();
        $cancelReason->reason = $request['CancelReason'];
        $cancelReason->type = $request['type'];
        if ($cancelReason->save())
            $message['success'] = 'دلیل لغو سفارش با موفقیت اضافه شد ';
        else
            $message['error'] = 'مجددا تلاش کنید ';

        return redirect()->back()->with($message);

    }

    function showEditCancelReasonForm($cancelReason_id)
    {

        $cancelReason = CancelReason::find($cancelReason_id);

        $data['cancelReason']= $cancelReason;
        $data['page_title']='ویرایش دلیل لغو سفارش';



        return view('admin.pages.cancel_reason.editCancelReason')->with($data);

    }

    function editCancelReason(Request $request){
        $this->validate($request,[
            'CancelReason' => 'required',
            'type' => 'required',
        ]);

        $cancelReason = CancelReason::find($request['idCancelReason']);


        $cancelReason->reason = $request->input('CancelReason');
        $cancelReason->type = $request->input('type');



        if ($cancelReason->save())
            $message['success'] = 'دلیل لغو سفارش با موفقیت ویرایش شد ';
        else
            $message['error'] = 'مجددا تلاش کنید ';


        return redirect()->route('admin.cancel.reason.list')->with($message);

    }

    function deleteCancelReason($cancelReason_id){

        if (CancelReason::destroy($cancelReason_id))
            $message['success'] = 'دلیل لغو سفارش با موفقیت حذف شد ';
        else
            $message['error'] = 'مجددا تلاش کنید ';

        return redirect()->back()->with($message);





    }



}
