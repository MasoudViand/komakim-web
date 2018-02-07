<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Category;
use App\Subcategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;

class UserAdminController extends Controller
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
        $userAdmins =Admin::all();


        $data['userAdmins']=$userAdmins;


        return view('admin.pages.user_admin.index')->with($data);;

        }

        function addForm()
        {
            $data['page_title']='افزودن کاربر ادمین';

            return view('admin.pages.user_admin.add_user_admin')->with($data);

        }

        function add(Request $request)
        {

            // dd($request['imageّIcon']);
            $this->validate($request, [
                'username' => 'required',
                'role' => 'required',
                'email' => 'required|email',
                'password' => 'required|string|min:6|confirmed',]);


            if (Admin::where('email',$request->get('email'))->first())
            {
                $message['error'] = 'این ایمیل قبلا ثبت شده';
                return redirect()->back()->with($message);
            }

           $userAdmin=new Admin();
           $userAdmin->name = $request->get('username');
           $userAdmin->email = $request->get('email');
           $userAdmin->role = $request->get('role');
           $userAdmin->password = bcrypt($request->get('password'));

            if ($userAdmin->save()) {
                $message['success'] = 'کاربر ادمین با موفقیت اضافه شد ';

            } else
                $message['error'] = 'مجددا تلاش کنید ';

            return redirect()->back()->with($message);


        }

        function showEditForm($admin_user_id)
        {
            $userAdmin = Admin::find($admin_user_id);




            $data['userAdmin'] = $userAdmin;
            $data['page_title']='ویرایش کاربر ادمین ';

            return view('admin.pages.user_admin.edit_user_admin')->with($data);
        }

        function edit(Request $request)
        {
            $this->validate($request, [
                'username' => 'required',
                'role' => 'required',
                'email' => 'required|email',]);






            if (!is_null($request->input('password')))
            {
                if (!is_string($request->input('password')))
                {
                    {
                        $message['error'] = 'در صورت پر کردن فیلد پسورد باید مقدار string ';
                        return redirect()->back()->with($message);
                    }
                }



                if (strlen($request->input('password'))<6)
                {
                    $message['error'] = 'در صورت پر کردن فیلد پسورد باید مقدار پسورد حداقل شش رقم باشد ';
                    return redirect()->back()->with($message);
                }

                if ($request->input('password')!=$request->input('password_confirmation'))
                {
                    $message['error'] = 'در صورت پر کردن فیلد پسورد پر کردن فیلد تایید الزامیست ';
                    return redirect()->back()->with($message);
                }
            }


            if (Admin::where('email',$request->get('email'))->first())
            {
                $user = Admin::where('email',$request->get('email'))->first();


                if ($user->id != $request->input('id'))
                {
                    $message['error'] = 'این ایمیل به نام کاربری دیگر قبلا ثبت شده';
                    return redirect()->back()->with($message);
                }



            }


            $userAdmin = Admin::find($request->input('id'));
            if (!is_null($request->input('password')))
                $userAdmin->password = bcrypt($request->input('password'));
            $userAdmin->email = $request->input('email');
            $userAdmin->role  = $request->input('role');
            $userAdmin->name = $request->input('username');


            if ($userAdmin->save())
            {


                $message['success'] = ';کاربر ادمین با موفقیت ویرایش شد ';
            }
                else
                $message['error'] = 'مجددا تلاش کنید ';
            return redirect()->route('admin.user_admin')->with($message);

        }

        function delete($admin_user_id,Request $request)
        {
           
            if ($request->user()->id ==$admin_user_id) {
                $message['error'] = 'این کاربر خودتان هستید';
                return redirect()->back()->with($message);
            }

            if (Admin::destroy($admin_user_id)){
                $message['success'] = ';کاربر ادمین  با موفقیت جذف شد ';

            }
            else
                $message['error'] = 'مجددا تلاش کنید ';

            return redirect()->back()->with($message);
        }


}
