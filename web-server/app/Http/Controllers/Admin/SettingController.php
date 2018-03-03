<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\SendNotificationToSingleUserJobWithFcm;
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

}
