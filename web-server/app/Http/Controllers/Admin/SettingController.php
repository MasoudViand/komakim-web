<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\SendNotificationToSingleUserJobWithFcm;
use App\RepeatQuestion;
use App\Setting;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use MongoDB\BSON\UTCDateTime;
use function MongoDB\read_concern_as_document;

class SettingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:admin','admin'])->except(['editRadiusSearch','editCommission']);
    }



    public function index()
    {
        $raduis =       Setting::where('type','radius')->first();
        $commission =   Setting::where('type','commission')->first();
        $appWorkerVerison = Setting::where('type','app_version')->where('app_type',User::WORKER_ROLE)->first();
        $appClientVerison = Setting::where('type','app_version')->where('app_type',User::CLIENT_ROLE)->first();

        if ($raduis)
            $raduis=$raduis->value;
        else {
            $raduisModel =new \stdClass();
            $raduisModel->type='radius';
            $raduisModel->value=1000;
            $raduisModel->created_at = new UTCDateTime(time()*1000);
            $raduisModel->updated_at = new UTCDateTime(time()*1000);

            $model = Setting::raw()->insertOne($raduisModel);
            $raduis=$raduisModel->value;
        }
        $data['radius']=$raduis;

        if ($commission)
            $commission=$commission->value;
        else {
            $raduisModel =new \stdClass();
            $raduisModel->type='commission';
            $raduisModel->value=5000;
            $raduisModel->created_at = new UTCDateTime(time()*1000);
            $raduisModel->updated_at = new UTCDateTime(time()*1000);

            $model = Setting::raw()->insertOne($raduisModel);
            $commission=$raduisModel->value;
        }
        $data['commission']=$commission;
        if (!$appWorkerVerison)
        {
            $appWorkerVerison =new \stdClass();;
            $appWorkerVerison->type='app_version';
            $appWorkerVerison->app_type=User::WORKER_ROLE;
            $appWorkerVerison->version='1.0.0';
            $appWorkerVerison->force_update=false;
            $appWorkerVerison->download_url='www.google.com';
            $appWorkerVerison->created_at = new UTCDateTime(time()*1000);
            $appWorkerVerison->updated_at = new UTCDateTime(time()*1000);

            $model = Setting::raw()->insertOne($appWorkerVerison);


        }


        $data['appWorkerVerison']=$appWorkerVerison;
        if (!$appClientVerison)
        {
            $appClientVerison =new \stdClass();
            $appClientVerison->type='app_version';
            $appClientVerison->app_type=User::CLIENT_ROLE;
            $appClientVerison->version='1.0.0';
            $appClientVerison->force_update=false;
            $appClientVerison->download_url='www.google.com';
            $appClientVerison->created_at = new UTCDateTime(time()*1000);
            $appClientVerison->updated_at = new UTCDateTime(time()*1000);

            $model = Setting::raw()->insertOne($appClientVerison);

        }


        $data['appClientVerison']=$appClientVerison;

        $data['page_title']='تنظیمات دیگر';

        return view('admin.pages.setting.index')->with($data);
    }
    public function editRadiusSearch( Request $request)
    {
        $content = $request->getContent();


        $content =(json_decode($content));

        $radius =$content->radius;

        $radius=(int)$radius;

        if (($radius)==0)
            return;

        $setting =Setting::where('type','radius')->first();

        if (!$setting){
            $setting = new Setting();
            $setting->type ='radius';

        }
        $setting->value=(int)$radius;

        $setting->save();
        return response()->json(['setting'=>$setting]);



    }
    public function editCommission( Request $request)
    {
        $content = $request->getContent();


        $content =(json_decode($content));

        $commission =$content->commission;

        $commission=(int)$commission;

        if (($commission)==0)
            return;

        $setting =Setting::where('type','commission')->first();

        if (!$setting){
            $setting = new Setting();
            $setting->type ='commission';

        }
        $setting->value=(int)$commission;

        $setting->save();
        return response()->json(['setting'=>$setting]);



    }

    function showEditVersionForm($id)
    {
        $version=Setting::find($id);
        $data['version']=$version;

        return view('admin.pages.setting.edit_version')->with($data);


    }

    function editVersion(Request $request)
    {
        $this->validate($request,
            [
                'version' => 'required',
                'download_url' => 'required'
            ]);

        $force_update=$request->input('force_update')=='true'?true:false;

        $version=Setting::find($request->input('idVersion'));

        $version->version= $request->input('version');
        $version->download_url= $request->input('download_url');
        $version->force_update= $force_update;

        if ($version->save())
            $message['success'] = 'ورزن با موفقیت ویرایش شد ';
        else
            $message['error'] = 'مجددا تلاش کنید';



        return redirect()->route('admin.setting')->with($message);





    }

    function showWorkWithUsConditionForm()
    {

        $workWithUsCondition=Setting::where('type','workWithUsCondition')->first();

        $text=null;

        if ($workWithUsCondition)
            $text=$workWithUsCondition->value;
        $data['text']=$text;


        return view('admin.pages.setting.work_with_us_condition')->with($data);


    }
    function EditWorkWithUsConditionForm(Request $request)
    {
        $this->validate($request,
            [

                'workWithUsCondition' => 'required'
            ]);

        $workWithUsCondition=Setting::where('type','workWithUsCondition')->first();

        if (!$workWithUsCondition)
        {
            $workWithUsCondition= new Setting();
            $workWithUsCondition->type='workWithUsCondition';
            $workWithUsCondition->value= $request->input('workWithUsCondition');
            $workWithUsCondition->created_at = new UTCDateTime(time()*1000);
            $workWithUsCondition->updated_at = new UTCDateTime(time()*1000);

        }else
        {
            $workWithUsCondition->value= $request->input('workWithUsCondition');
            $workWithUsCondition->updated_at = new UTCDateTime(time()*1000);

        }

        if ($workWithUsCondition->save())
            if ($workWithUsCondition->save())
                $message['success'] = 'ورزن با موفقیت ویرایش شد ';
            else
                $message['error'] = 'مجددا تلاش کنید';



        return redirect()->route('admin.setting')->with($message);


    }

    function showRolesForm()
    {

        $rules=Setting::where('type','rules')->first();

        $text=null;

        if ($rules)
            $text=$rules->value;
        $data['text']=$text;


        return view('admin.pages.setting.rules')->with($data);


    }

    function editRoles(Request $request)
    {
        $this->validate($request,
            [

                'rules' => 'required'
            ]);

        $rules=Setting::where('type','rules')->first();

        if (!$rules)
        {
            $rules= new Setting();
            $rules->type='rules';
            $rules->value= $request->input('rules');
            $rules->created_at = new UTCDateTime(time()*1000);
            $rules->updated_at = new UTCDateTime(time()*1000);

        }else
        {
            $rules->value= $request->input('rules');
            $rules->updated_at = new UTCDateTime(time()*1000);

        }


            if ($rules->save())
                $message['success'] = 'ورزن با موفقیت ویرایش شد ';
            else
                $message['error'] = 'مجددا تلاش کنید';



        return redirect()->route('admin.setting')->with($message);

    }

    function ListRepeatQuestions()
    {
        $repeadQuestions = RepeatQuestion::paginate(15);

        $data['repeadQuestions']=$repeadQuestions;

        return view('admin.pages.setting.repeat_question_list')->with($data);


    }
    function ShowRepeatQuestionsForm()
    {
        return view('admin.pages.setting.repeat_question_form');


    }

    function CreateRepeatQuestions(Request $request){


        $this->validate($request,
            [

                'question' => 'required',
                'answer' => 'required'
            ]);

        $repeadQuestion= new  RepeatQuestion();

        $repeadQuestion->answer=$request->input('answer');
        $repeadQuestion->question = $request->input('question');

        if ($repeadQuestion->save())
            $message['success'] = 'سوال متداول با موفقیت ایجاد شد ';
        else
            $message['error'] = 'مجددا تلاش کنید';


        return redirect()->back()->with($message);




    }

    function ShowEditRepeatQuestionsForm($repeat_question_id)
    {
        $repeatQuestion=RepeatQuestion::find($repeat_question_id);

        $data['repeatQuestion']=$repeatQuestion;

        return view('admin.pages.setting.edit_repeat_question_form')->with($data);
    }
    function edit(Request $request)
    {
        $this->validate($request,
            [

                'question' => 'required',
                'answer' => 'required'
            ]);



        $repeatQuestion=RepeatQuestion::find($request->input('id'));
        $repeatQuestion->answer=$request->input('answer');
        $repeatQuestion->question=$request->input('question');
         if ($repeatQuestion->save())
             $message['success'] = 'سوال متداول با موفقیت ویرایش شد ';
         else
             $message['error'] = 'مجددا تلاش کنید';

        return redirect()->route('admin.repeat.question')->with($message);






    }
    function delete($repeat_question_id)
    {
        if(RepeatQuestion::destroy($repeat_question_id))
            $message['success'] = 'سوال با موفقیت حذف شد';
        else
            $message['error'] = 'مجددا تلاش کن';

        return redirect()->back()->with($message);

    }

}
