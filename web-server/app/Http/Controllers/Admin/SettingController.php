<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        if ($raduis)
            $raduis=$raduis->value;
        else $raduis = 1000;
        $data['radius']=$raduis;

        if ($commission)
            $commission=$commission->value;
        else $commission = 5000;
        $data['commission']=$commission;

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
