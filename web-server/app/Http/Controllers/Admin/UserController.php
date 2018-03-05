<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\DissatisfiedReason;
use App\Review;
use App\User;
use App\Wallet;
use App\WorkerProfile;
use bar\baz\source_with_namespace;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use MongoDB\BSON\ObjectID;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin','operator'])->except('filterUser');
    }

    function index(Request $request){


        $limit =10;
        $query=[];
        $queryParam=[];
        $fields =Category::all();
        $data['fields']=$fields;

        if ($request->has('page'))
        {
            $queryParam['page']=(int)$request->input('page');
            $skip =((int)$request->input('page')-1)*$limit;

        }
        else
        {
            $skip = 0;
            $queryParam['page']=1;

        }

        if ($request->has('phone_number'))
        {
            $query['phone_number']=$request->input('phone_number');
            $queryParam['phone_number']=$request->input('phone_number');

        }

        if ($request->has('role'))
        {
            $query['role']=$request->input('role');
            $queryParam['role']=$request->input('role');


        }


        if ($request->has('email'))
        {
            $query['email']=$request->input('email');
            $queryParam['email']=$request->input('email');




        }

        if ($request->has('status'))
        {
            $query['status']=$request->input('status');
            $queryParam['status']=$request->input('status');


        }

        if ($request->has('national_code'))
        {
            $query['profile.national_code']=$request->input('national_code');
            $queryParam['national_code']=$request->input('national_code');


        }


        if ($request->has('fields'))
        {
           $fields = explode(',',$request->input('fields'));

            $query['profile.fields']= [ '$in' => $fields];
            $queryParam['fields']=$fields;


        }

        if ($request->has('gender'))
        {
            $query['profile.gender']=$request->input('gender');
            $queryParam['gender']=$request->input('gender');


        }

        if ($request->has('admin_status'))
        {
            $query['profile.status']=$request->input('admin_status');
            $queryParam['admin_status']=$request->input('admin_status');


        }


        if ($request->has('availability_status'))
        {
            $query['profile.availabilitystatus']=$request->input('availability_status');
            $queryParam['availability_status']=$request->input('availability_status');


        }


        $q = [
                ['$sort'=>['_id'=>-1]],
                [ '$skip' => $skip ],
                [ '$limit' => $limit ],

                [ '$lookup' => [
                    'from'         => 'worker_profiles',
                    'localField'   => '_id',
                    'foreignField' => 'user_id',
                    'as'           => 'profile',],

                ],
            [ '$lookup' => [
                'from'         => 'wallets',
                'localField'   => '_id',
                'foreignField' => 'user_id',
                'as'           => 'wallet',],

            ],

        ];

        $q_count= [
                    [ '$lookup' => [
                        'from'         => 'worker_profiles',
                        'localField'   => '_id',
                        'foreignField' => 'user_id',
                        'as'           => 'profile',],

                    ],

        ];


        if (count($query)>0)
        {
            $q[]= ['$match' => $query ];
            $q_count[]= ['$match' => $query ];
        }


        if ($request->has('sort')) {
            $queryParam['sort']=$request->input('sort');
            array_shift($q);
            if ($request->input('sort') == 'desc') {

                $ql=[
                    '$sort' =>[
                        'profile.mean_score' =>1
                    ]
                ];
                array_unshift($q,$ql);
            } else {

                $ql=[
                    '$sort' =>[
                        'profile.mean_score' =>-1
                    ]
                ];
                array_unshift($q,$ql);
            }
        }



        $q_count[]=['$count'=>'count'];


        $model = User::raw()->aggregate($q);
        $count = User::raw()->aggregate($q_count);


        $userArr=[];
        $countArr=[];
        foreach ($model as $item)
        {
            array_push($userArr,$item);
        }


        foreach ($count as $item)
        {
            array_push($countArr,$item);
        }


        $data['users']=$userArr;
        if (count($countArr)>0)
            $data['count']=$countArr[0]['count'];
        else
            $data['count']=0;



        $data['queryParam']=$queryParam;
        $data['page_title']='کاربران';
        $data['total_page']=(int)($data['count']/$limit)+1;





        return view('admin.pages.user.listUser')->with($data);
    }

    function filterUser(Request $request)
    {

        $content = $request->getContent();

        $content =(json_decode($content));

        $query=[];

        if (isset($content->mobile))
            $query['phone_number']=$content->mobile;
        if (isset($content->role))
            $query['role']=$content->role;
        if (isset($content->email))
            $query['email']=$content->email;
        if (isset($content->status))
            $query['status']=$content->status;
        if (isset($content->national_code))
            $query['profile.national_code']=$content->national_code;
        if (isset($content->field))
            $query['profile.field'] =$content->field;
        if (isset($content->gender))
            $query['profile.gender']=$content->gender;
        if (isset($content->admin_status))
            $query['profile.status']=$content->admin_status;
        if (isset($content->availabilitystatus))
            $query['profile.availabilitystatus']=$content->availabilitystatus;



      //  dd($query);

        $q = [
            [ '$limit' => 10 ],


            [ '$lookup' => [
                'from'         => 'worker_profiles',
                'localField'   => '_id',
                'foreignField' => 'user_id',
                'as'           => 'profile',],

            ],

            [
                '$match' => $query            ]


        ];

        if (isset($content->sort))
        {

            if ($content->sort=='desc')
            {
                $q[]=[ '$sort' => ['profile.mean_score' => 1], ];
            }
            else{

                $q[]=[ '$sort' => ['profile.mean_score' => -1], ];
            }

        }
        $model = User::raw()->aggregate($q);
        $userArr=[];
        foreach ($model as $item)
        {
            $item->_id=(string)$item->_id;
            array_push($userArr,$item);
        }
       // dd($userArr);

        return response()->json($userArr);

        return json_encode($userArr,true);


    }



    function showEditUserForm($user_id)
    {
        $user = User::find($user_id);
        $wallet = Wallet::where('user_id',new ObjectID($user_id))->first();

        if ($wallet)
            $wallet = $wallet->amount;
        else
            $wallet =0;
        $data['user']=$user;
        $workerProfile=null;
        if ($user->role==User::WORKER_ROLE)
        {
            $workerProfile=WorkerProfile::where('user_id',new ObjectID($user_id))->first();
            $filepath=URL::to('/').'/images/workers/profile-default-male.png';

            if ($workerProfile){
                if (file_exists((public_path('images/workers').'/'.$workerProfile->id).'.png'))
                    $filepath=('images/workers').'/'.$workerProfile->id.'.png';
                if (file_exists((public_path('images/workers').'/'.$workerProfile->id).'.jpg'))
                    $filepath=('images/workers').'/'.$workerProfile->id.'.jpg';
                if (file_exists((public_path('images/workers').'/'.$workerProfile->id).'.jpeg'))
                    $filepath=('images/workers').'/'.$workerProfile->id.'.jpeg';


                $data['filepath']=URL::to('/').'/'.$filepath;


                $date = \Morilog\Jalali\jDateTime::strftime('Y/m/d', strtotime($workerProfile->birthDay['date']));
                $data['date']=$date;
                if ($workerProfile->status==WorkerProfile::WORKER_PENDING_STATUS)
                    $workerProfileStatus='منتظر تایید';
                elseif ($workerProfile->status==WorkerProfile::WORKER_REJECT_STATUS)
                    $workerProfileStatus ='رد شده';
                else
                    $workerProfileStatus='تایید شده';
                $data['workerProfileStatus']=$workerProfileStatus;

            }

            $lastReview = Review::where('worker_id',new ObjectID($user->id))->orderBy('_id', 'desc')->first();

            $meanReview = '--';

            if ($lastReview)
            {
                $meanReview = $lastReview->mean_score;
            }


            $data['meanReview']=round($meanReview,1);


            $data['workerProfile']=$workerProfile;

            }
        $data['workerProfile']=$workerProfile;
        $data['wallet']=$wallet;
        $data['page_title']='ویرایش کاربران';
        $fields =Category::all();
        $data['fields']=$fields;

        return view('admin.pages.user.editUser')->with($data);
    }

    function editUser(Request $request)
    {

        $this->validate($request,[
            'nameUser' => 'required',
            'familyUser' => 'required',
            'mobileUser' => 'required',
        ]);

        $userBynumber=User::where('phone_number',$request['mobileUser'])->first();
        if ($userBynumber){
            if (!($userBynumber->id==$request['idUser'])){
                $message['error']='کاربری با این شماره تلفن وجود دارد';
                return redirect()->back()->with($message);
            }
        }



        $user = User::find($request['idUser']);
        $user->name=$request['nameUser'];
        $user->family=$request['familyUser'];
        $user->phone_number=$request['mobileUser'];
        $user->email=$request['emailUser'];
        $user->password =bcrypt($request['mobileUser']);
        $user->status= $request['status'];
        if ($user->save())
            $message['success']='یوزر با موفقیت ویرایش شد';
        else $message['error']='مجددا تلاش کنید';
        return redirect()->back()->with($message);
    }
    function editWorkerProfile(Request $request)
    {
        //dd($request['imageProfile']);
        $this->validate($request,[
            'nationCodeProfile' => 'required',
            'phoneProfile' => 'required',
            'addressProfile' => 'required',
            'birthdayProfile' => 'required',
            'imageProfile'   => 'image|mimes:jpeg,png,jpg|max:512'
        ]);




        if (!($this->CheckNationalCode($request['nationCodeProfile'])))
        {

            $message['error']='کد ملی وارد شده صحیح نمیباشذ';
            return redirect()->back()->with($message);
        }




        $workerProfile = WorkerProfile::where('user_id',new ObjectID($request['idUser']))->first();
        $workerProfileByNationalCode = WorkerProfile::where('nationalCode',$request['nationCodeProfile'])->first();
        if ($workerProfileByNationalCode)
        {
            if (!($workerProfileByNationalCode->id=$workerProfile->id))
            {
                $message['error']='کد ملی وارد شده اختصاص به کاربری دیگر دارد';
                return redirect()->back()->with($message);
            }
        }



       if ($request['imageProfile'])
       {
           $imageName = $workerProfile->id.'.'.request()->imageProfile->getClientOriginalExtension();

           if (file_exists((public_path('images/workers').'/'.$workerProfile->id).'.png'))
               unlink((public_path('images/workers').'/'.$workerProfile->id).'.png');
           if (file_exists((public_path('images/workers').'/'.$workerProfile->id).'.jpg'))
               unlink((public_path('images/workers').'/'.$workerProfile->id).'.jpg');
           if (file_exists((public_path('images/workers').'/'.$workerProfile->id).'.png'))
               unlink((public_path('images/workers').'/'.$workerProfile->id).'.png');

           request()->imageProfile->move(public_path('images/workers'), $imageName);
           $path=(public_path('images/workers').'/'.$imageName);
           $file =fopen($path, 'r');
       }


        $workerProfile->national_code        =$request['nationCodeProfile'];
        $workerProfile->address             =$request['addressProfile'];
        $workerProfile->home_phone_number   =$request['phoneProfile'];
        $workerProfile->fields               =$request['fields'];
        $workerProfile->last_education      =$request['lastEducationProfile'];
        $workerProfile->gender              =$request['gender'];
        $workerProfile->status              =$request['statusProfile'];
        $workerProfile->marriage_status     =$request['marriage_status'];
        $workerProfile->another_capability  =$request['anotherCapabilityProfile'];
        $workerProfile->certificates        =$request['certificatesProfile'];
        $workerProfile->experience          =$request['experienceProfile'];
        $workerProfile->birthDay            =\Morilog\Jalali\jDateTime::createDatetimeFromFormat('Y/m/d H:i:s', $request['birthdayProfile'].' 00:00:00');

        if ($workerProfile->save())
        {
            $message['success']='پروفایل کاربر به درستی ویرایش شد';
        }else
            $message['error']='مجددا تلاش کنید';

        return redirect()->back()->with($message);


    }
    function listReviewUser($worker_id)
    {


        $reviewModel =Review::where('worker_id',new ObjectID($worker_id))->paginate(15);

        $reviewArr=[];

        foreach ($reviewModel as $item)
        {
            $review=[];
            $review['user']=User::find($item->user_id);
            $review['score']=$item['score'];
            $reasons=[];

            foreach ($item->reasons as $reason)
            {
                array_push($reasons ,DissatisfiedReason::find($reason));
            }


            $review['reasons']=$reasons;
            $review['desc'] =$item->desc;
            $review['order_id'] =(string)$item->order_id;

            array_push($reviewArr,$review);

        }

        $data['reviews']=$reviewArr;
        $data['reviewModel'] =$reviewModel;

        return view('admin.pages.user.list_review')->with($data);



    }

    function checkNationalCode($code='')
    {
        $code = (string) preg_replace('/[^0-9]/','',$code);
        if(strlen($code)>10 or strlen($code)<8)
            return false;

        if(strlen($code)==8)
            $code = "00".$code;

        if(strlen($code)==9)
            $code = "0".$code;

        $list_code = str_split($code);
        $last = (int) $list_code[9];
        unset($list_code[9]);
        $i = 10;
        $sum = 0;
        foreach($list_code as $key=>$_)
        {

            $sum += intval($_) * $i;
            $i--;
        }

        $mod =(int) $sum % 11;

        if($mod >= 2)
            $mod = 11 - $mod;

        if( $mod != $last)
            return false;

        for($i=0;$i<10;$i++)
        {
            $str = str_repeat($i,10);
            if($str==$code)
                return false;
        }

        return true;
    }

}
