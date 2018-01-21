<?php

namespace App\Http\Controllers\Admin;

use App\DissatisfiedReason;
use Defuse\Crypto\Exception\IOException;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mockery\Exception;

class DissatisfiedReasonController extends Controller
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

    function index()
    {



        $dissatisfiedReasons = DissatisfiedReason::paginate(15);

        $data['dissatisfiedReasons'] = $dissatisfiedReasons;
        $data['total_count'] = DissatisfiedReason::count();

        return view('admin.pages.dissatisfied_reason.list_dissatisfied_reason')->with($data);


    }

    function addDissatisfiedReasonForm(){


        return view('admin.pages.dissatisfied_reason.addDissatisfiedReason');

    }

    function addDissatisfiedReason(Request $request)
    {

        $this->validate($request,[
            'DissatisfiedReason' => 'required',
        ]);

        $dissatisfiedReason = new DissatisfiedReason();
        $dissatisfiedReason->reason = $request['DissatisfiedReason'];
        if ($dissatisfiedReason->save())
            $message['success'] = 'دلیل عدم زضایت با موفقیت اضافه شد ';
        else
            $message['error'] = 'مجددا تلاش کنید ';

        return redirect()->back()->with($message);

    }

    function showEditDissatisfiedReasonForm($dissatisfiedReason_id)
    {
        $dissatisfiedReason = DissatisfiedReason::find($dissatisfiedReason_id);

        $data['dissatisfiedReason']= $dissatisfiedReason;

        return view('admin.pages.dissatisfied_reason.editDissatisfiedReason')->with($data);

    }

    function editDissatisfiedReason(Request $request){
        $this->validate($request,[
            'DissatisfiedReason' => 'required',
        ]);

        $dissatisfiedReason = DissatisfiedReason::find($request['idDissatisfiedReason']);


        $dissatisfiedReason->reason = $request['DissatisfiedReason'];



        if ($dissatisfiedReason->save())
            $message['success'] = 'دلیل عدم زضایت با موفقیت ویرایش شد ';
        else
            $message['error'] = 'مجددا تلاش کنید ';


        return redirect()->route('admin.dissatisfied.reason.list')->with($message);

    }

    function deleteDissatisfiedReason($dissatisfiedReason_id){

        if (DissatisfiedReason::destroy($dissatisfiedReason_id))
            $message['success'] = 'دلیل عدم رضایت با موفقیت حذف شد ';
        else
            $message['error'] = 'مجددا تلاش کنید ';

        return redirect()->back()->with($message);





    }



}
