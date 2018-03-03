<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\DiscountCode;
use App\Jobs\SendSmsToUsersJob;
use App\Subcategory;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
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




        $data['page_title']='ارسال ناتیفیکایشن';

        return view('admin.pages.notification.index')->with($data);

    }

     function sendNotification(Request $request)
    {
        $this->validate($request, ['content' => 'required']);



        if ($request->get('type')==User::WORKER_ROLE)
        {

            $app_id ='5cf31f6e-0526-4083-841b-03d789183ab8';
            $authorization = 'Authorization: Basic Yzc0M2E3NzItYjZmMS00MDg4LWJiZDAtMjZkZWI4NDJmNDhi';

        }
        else
        {
            $app_id= '5cf31f6e-0526-4083-841b-03d789183ab8';
            $authorization = 'Authorization: Basic Yzc0M2E3NzItYjZmMS00MDg4LWJiZDAtMjZkZWI4NDJmNDhi';


        }
        $content = array(
            "en" => $request->get('content')
        );

        $fields = array(
            'app_id' => $app_id,
            'included_segments' => array('All'),

            'contents' => $content
        );

        $fields = json_encode($fields);
        print("\nJSON sent:\n");
        print($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            $authorization));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response);

        $recipients = $response->recipients;
         $userType= 'مشتری';
         if ($request->input('type')==User::WORKER_ROLE)
             $userType ='خدمه';
        $message['success'] = 'ناتیفیکایشن با موفقیت برای '.$recipients.' کاربر'.$userType.' ارسال شد ';








        return redirect()->back()->with($message);


    }

     function showsmsform ()
    {




        $data['page_title']='ارسال پیام کوتاه';
        return view('admin.pages.notification.sms_form')->with($data);
    }
    function sendSms(Request $request)
    {
        $this->validate($request, ['content' => 'required']);

        $user = new User();

        $user =$user->newQuery();

        $userType =User::CLIENT_ROLE;



        if ($request->input('type')==User::WORKER_ROLE)
            $userType =User::WORKER_ROLE;


        $user = $user->where('role',$userType);

        $userCount = $user->count();



        $limit = 1000;
        $skip = 0;

        while ($skip<$userCount)
        {

            $userModel = $user->skip($skip)->take($limit)->get();

            $numbers =[];

            foreach ($userModel as $item)
                $numbers[]=$item->phone_number;
            $this->dispatch(new SendSmsToUsersJob($numbers,$request->get('content')));
            $numberCount =count($numbers);
            $skip=$skip+$numberCount;
        }
        $role ='خدمه';
        if ($userType==User::CLIENT_ROLE)
            $role='مشتری';
        $message['success'] = 'پیام کوتاه با موفقیت برای '.$userCount.' کاربر'.$role.' ارسال شد ';

        return redirect()->back()->with($message);

    }
}
