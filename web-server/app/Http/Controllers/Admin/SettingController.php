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
        if ($appClientVerison)
            dd(1);
        else
        {
            $raduisModel =new \stdClass();
            $raduisModel->type='app_version';
            $raduisModel->app_type=5000;
            $raduisModel->created_at = new UTCDateTime(time()*1000);
            $raduisModel->updated_at = new UTCDateTime(time()*1000);

            $model = Setting::raw()->insertOne($raduisModel);
            $commission=$raduisModel->value;
        }

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

}
