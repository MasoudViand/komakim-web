<?php

namespace App\Http\Controllers\Admin;

use App\MailTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmailTemplateController extends Controller
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


    public function index()
    {
        $mailsTemplate =MailTemplate::paginate(15);

        $data['mailsTemplate'] = $mailsTemplate;
        $data['total_count'] = MailTemplate::count();
        $data['page_title']='قالب های ایمیل';


        return view('admin.pages.email.listEmailTemplate')->with($data);
    }
    public function addEmailTemplateForm()
    {
        $data['page_title']='افزودن قالب ایمیل';


        return view('admin.pages.email.addEmailTemplate')->with($data);
    }
    public function addEmailTemplate(Request $request)
    {
        $this->validate($request,[
            'nameEmailTemplate' => 'required',
            'Template' => 'required',
        ]);
        $message=null;
        $emailTemplate = new MailTemplate();
        $emailTemplate->name = $request['nameEmailTemplate'];
        $emailTemplate->html_email_template =$request['Template'];

        if($emailTemplate->save())
            $message['success'] = 'فالب با موفقیت اضافه شد';
        else
            $message['error'] = 'مجددا تلاش بفرمایید';



        return redirect()->back()->with($message);
    }

    public function showMialEditForm($mail_template_id)
    {
        $mailTemplate = MailTemplate::find($mail_template_id);
        $data['mailTemplate']=$mailTemplate;
        $data['page_title']='ویرایش قالب ایمیل';



        return view('admin.pages.email.editEmailTemplate')->with($data);

    }
    public function EditEmailTemplateForm(Request $request)
    {
        $this->validate($request,[
            'nameEmailTemplate' => 'required',
            'Template' => 'required',
        ]);
        $message=null;

        $emailTemplate=MailTemplate::find($request['mailTemplateId']);

        $emailTemplate->name =$request['nameEmailTemplate'];
        $emailTemplate->html_email_template = $request['Template'];
        if ($emailTemplate->save()){
            $message='قالب با موفقیت تغییر یافت';
            return redirect()->route('admin.list.email.template')->with($message);

        }

        else
        {
            $message='مجددا تلاش کنید';
            return redirect()->back()->with($message);

        }


    }
}
