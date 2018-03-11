<?php

namespace App\Http\Controllers\Admin;

use App\DissatisfiedReason;
use Defuse\Crypto\Exception\IOException;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;
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
        $this->middleware(['auth:admin','admin']);
    }

    function index()
    {


        $dissatisfiedReasons = DissatisfiedReason::orderBy('_id','desc')->paginate(20);

        foreach ($dissatisfiedReasons as $dissatisfiedReason) {
            $filepath =  '/images/icons/service-icon-default.jpg';


            if (file_exists((public_path('images/icons') . '/' . $dissatisfiedReason->id) . '.png')) $filepath = ('/images/icons') . '/' . $dissatisfiedReason->id . '.png';
            if (file_exists((public_path('images/icons') . '/' . $dissatisfiedReason->id) . '.jpg')) $filepath = ('/images/icons') . '/' . $dissatisfiedReason->id . '.jpg';
            if (file_exists((public_path('images/icons') . '/' . $dissatisfiedReason->id) . '.jpeg')) $filepath = ('/images/icons') . '/' . $dissatisfiedReason->id . '.jpeg';

            $filepath=URL::to('/') .''.$filepath;

            $dissatisfiedReason->filepath = $filepath;
        }
        $data['dissatisfiedReasons'] = $dissatisfiedReasons;
        $data['total_count'] = DissatisfiedReason::count();
        $data['page_title']='دلایل عدم رضایت';


        return view('admin.pages.dissatisfied_reason.list_dissatisfied_reason')->with($data);


    }

    function addDissatisfiedReasonForm(){

        $data['page_title']='اضافه کردن دلیل عدم رضایت';

        return view('admin.pages.dissatisfied_reason.addDissatisfiedReason')->with($data);

    }

    function addDissatisfiedReason(Request $request)
    {



        $this->validate($request,[
            'DissatisfiedReason' => 'required',
            'imageّIcon' => 'required|image|mimes:jpeg,png,jpg|max:512',
        ]);




        $dissatisfiedReason = new DissatisfiedReason();
        $dissatisfiedReason->reason = $request['DissatisfiedReason'];
        if ($dissatisfiedReason->save())
        {
            $imageName = $dissatisfiedReason->id . '.' . request()->imageّIcon->getClientOriginalExtension();

            if (file_exists((public_path('images/icons') . '/' . $dissatisfiedReason->id) . '.png')) unlink((public_path('images/icons') . '/' . $dissatisfiedReason->id) . '.png');
            if (file_exists((public_path('images/icons') . '/' . $dissatisfiedReason->id) . '.jpg')) unlink((public_path('images/icons') . '/' . $dissatisfiedReason->id) . '.jpg');
            if (file_exists((public_path('images/icons') . '/' . $dissatisfiedReason->id) . '.png')) unlink((public_path('images/icons') . '/' . $dissatisfiedReason->id) . '.jpeg');

            request()->imageّIcon->move(public_path('images/icons'), $imageName);
            $path = (public_path('images/icons') . '/' . $imageName);
            $file = fopen($path, 'r');
            $message['success'] = 'دلیل عدم زضایت با موفقیت اضافه شد ';

        }
        else
            $message['error'] = 'مجددا تلاش کنید ';

        return redirect()->back()->with($message);

    }

    function showEditDissatisfiedReasonForm($dissatisfiedReason_id)
    {
        $dissatisfiedReason = DissatisfiedReason::find($dissatisfiedReason_id);
        $filepath =  '/images/icons/service-icon-default.jpg';


        if (file_exists((public_path('images/icons') . '/' . $dissatisfiedReason->id) . '.png')) $filepath = ('/images/icons') . '/' . $dissatisfiedReason->id . '.png';
        if (file_exists((public_path('images/icons') . '/' . $dissatisfiedReason->id) . '.jpg')) $filepath = ('/images/icons') . '/' . $dissatisfiedReason->id . '.jpg';
        if (file_exists((public_path('images/icons') . '/' . $dissatisfiedReason->id) . '.jpeg')) $filepath = ('/images/icons') . '/' . $dissatisfiedReason->id . '.jpeg';

        $filepath=URL::to('/') .''.$filepath;

        $dissatisfiedReason->filepath = $filepath;

        $data['dissatisfiedReason']= $dissatisfiedReason;
        $data['page_title']='ویرایش دلیل عدم رضایت';


        return view('admin.pages.dissatisfied_reason.editDissatisfiedReason')->with($data);

    }

    function editDissatisfiedReason(Request $request){
        $this->validate($request,[
            'DissatisfiedReason' => 'required',
        ]);

        $dissatisfiedReason = DissatisfiedReason::find($request['idDissatisfiedReason']);

            $dissatisfiedReason->reason = $request['DissatisfiedReason'];



        if ($dissatisfiedReason->save())
        {
            if($request->has('imageّIcon')   )
            {


                $imageName = $dissatisfiedReason->id . '.' . request()->imageّIcon->getClientOriginalExtension();

                if (file_exists((public_path('images/icons') . '/' . $dissatisfiedReason->id) . '.png')) unlink((public_path('images/icons') . '/' . $dissatisfiedReason->id) . '.png');
                if (file_exists((public_path('images/icons') . '/' . $dissatisfiedReason->id) . '.jpg')) unlink((public_path('images/icons') . '/' . $dissatisfiedReason->id) . '.jpg');
                if (file_exists((public_path('images/icons') . '/' . $dissatisfiedReason->id) . '.png')) unlink((public_path('images/icons') . '/' . $dissatisfiedReason->id) . '.png');

                request()->imageّIcon->move(public_path('images/icons'), $imageName);
                $path = (public_path('images/icons') . '/' . $imageName);

                $file = fopen($path, 'r');

            }
            $message['success'] = 'دلیل عدم زضایت با موفقیت ویرایش شد ';


        }
        else
            $message['error'] = 'مجددا تلاش کنید ';


        return redirect()->route('admin.dissatisfied.reason.list')->with($message);

    }

    function deleteDissatisfiedReason($dissatisfiedReason_id){

        if (DissatisfiedReason::destroy($dissatisfiedReason_id))
        {
            if (file_exists((public_path('images/icons') . '/' . $dissatisfiedReason_id) . '.png')) unlink((public_path('images/icons') . '/' . $dissatisfiedReason_id) . '.png');
            if (file_exists((public_path('images/icons') . '/' . $dissatisfiedReason_id) . '.jpg')) unlink((public_path('images/icons') . '/' . $dissatisfiedReason_id) . '.jpg');
            if (file_exists((public_path('images/icons') . '/' . $dissatisfiedReason_id) . '.png')) unlink((public_path('images/icons') . '/' . $dissatisfiedReason_id) . '.png');
            $message['success'] = 'دلیل عدم رضایت با موفقیت حذف شد ';

        }
        else
            $message['error'] = 'مجددا تلاش کنید ';

        return redirect()->back()->with($message);





    }



}
