<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Subcategory;
use App\User;
use App\WorkerProfile;
use Couchbase\UserSettings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;

class MapController extends Controller
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

    public function index(Request $request)
    {

        $query=[];
        $query['status']=User::ENABLE_USER_STATUS;
        $query['role']  =User::WORKER_ROLE;
        $query['profile.availability_status']=WorkerProfile::WORKER_AVAILABLE_STATUS;
        $query['profile.status'] ='accept';
        $queryParam=[];
        $fields =Category::all();
        $data['fields']=$fields;
        $data['page_title']='نقشه';


        if (!$request->has('phone_number')and !$request->has('national_code') and !$request->has('fields') and !$request->has('fields') and !$request->has('gender')){
            $locations=[];
            $data['locations']=$locations;
            $data['queryParam']=$queryParam;

            return view('admin.pages.map.index')->with($data);
        }


        if ($request->has('phone_number'))
        {
            $query['phone_number']=$request->input('phone_number');
            $queryParam['phone_number']=$request->input('phone_number');

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




        $q = [
            [ '$skip' => 0 ],
            [ '$limit' => 100 ],

            [ '$lookup' => [
                'from'         => 'worker_profiles',
                'localField'   => '_id',
                'foreignField' => 'user_id',
                'as'           => 'profile',],

            ],

        ];

        $q[]= ['$match' => $query ];


        $model = User::raw()->aggregate($q);

      //  dd($q);



        $locations = [];


        foreach ($model as $workerprofile)
        {



            if (key_exists('location',$workerprofile->profile[0]))
            {
               $lan = $workerprofile->profile[0]['location']['coordinates'][1];
               $long = $workerprofile->profile[0]['location']['coordinates'][0];
               $name = $workerprofile->name.' '.$workerprofile->family;

               $location =['lat'=>$lan ,'lng'=>$long,'name'=>$name];
               array_push($locations,$location);
            }
        }




        $data['locations']=$locations;
        $data['queryParam']=$queryParam;



        return view('admin.pages.map.index')->with($data);

        }

        function addCategoryForm()
        {
            $data['page_title']='افزودن دسته بندی';

            return view('admin.pages.map.idex')->with($data);

        }

        function addCategory(Request $request)
        {

            // dd($request['imageّIcon']);
            $this->validate($request, ['nameCategory' => 'required', 'statusCategory' => 'required', 'orderCategory' => 'required|numeric', 'imageّIcon' => 'required|image|mimes:jpeg,png,jpg|max:512',]);

            $category = new Category();
            $category->name = $request['nameCategory'];
            $category->status = $request['statusCategory'] === 'true' ? true : false;
            $category->order = (int)$request['orderCategory'];

            if ($category->save()) {
                $imageName = $category->id . '.' . request()->imageّIcon->getClientOriginalExtension();

                if (file_exists((public_path('images/icons') . '/' . $category->id) . '.png')) unlink((public_path('images/icons') . '/' . $category->id) . '.png');
                if (file_exists((public_path('images/icons') . '/' . $category->id) . '.jpg')) unlink((public_path('images/icons') . '/' . $category->id) . '.jpg');
                if (file_exists((public_path('images/icons') . '/' . $category->id) . '.png')) unlink((public_path('images/icons') . '/' . $category->id) . '.png');

                request()->imageّIcon->move(public_path('images/icons'), $imageName);
                $path = (public_path('images/icons') . '/' . $imageName);
                $file = fopen($path, 'r');
                $message['success'] = 'دسته بندی با موفقیت اضافه شد ';

            } else
                $message['error'] = 'مجددا تلاش کنید ';

            return redirect()->back()->with($message);


        }

        function showEditCategoryForm($category_id)
        {
            $category = Category::find($category_id);
            $filepath =  '/images/icons/service-icon-default.jpg';


            if (file_exists((public_path('images/icons') . '/' . $category->id) . '.png')) $filepath = ('/images/icons') . '/' . $category->id . '.png';
            if (file_exists((public_path('images/icons') . '/' . $category->id) . '.jpg')) $filepath = ('/images/icons') . '/' . $category->id . '.jpg';
            if (file_exists((public_path('images/icons') . '/' . $category->id) . '.jpeg')) $filepath = ('/images/icons') . '/' . $category->id . '.jpeg';

            $filepath=URL::to('/') .''.$filepath;

            $category->filepath = $filepath;
            $data['category'] = $category;
            $data['page_title']='ویرایش دسته بندی ';

            return view('admin.pages.category.editCategory')->with($data);
        }

        function editCategory(Request $request)
        {
            $this->validate($request, [
                'nameCategory' => 'required',
                'statusCategory' => 'required',
                'orderCategory' => 'required|numeric']);



            $category = Category::find($request['idCategory']);
            $category->name = $request['nameCategory'];
            $category->status = $request['statusCategory'] === 'true' ? true : false;
            $category->order = (int)$request['orderCategory'];

            if ($category->save())
            {
                if($request->has('imageّIcon')   )
                {


                    $imageName = $category->id . '.' . request()->imageّIcon->getClientOriginalExtension();

                    if (file_exists((public_path('images/icons') . '/' . $category->id) . '.png')) unlink((public_path('images/icons') . '/' . $category->id) . '.png');
                    if (file_exists((public_path('images/icons') . '/' . $category->id) . '.jpg')) unlink((public_path('images/icons') . '/' . $category->id) . '.jpg');
                    if (file_exists((public_path('images/icons') . '/' . $category->id) . '.png')) unlink((public_path('images/icons') . '/' . $category->id) . '.png');

                    request()->imageّIcon->move(public_path('images/icons'), $imageName);
                    $path = (public_path('images/icons') . '/' . $imageName);

                    $file = fopen($path, 'r');

                }

                $message['success'] = 'دسته بندی با موفقیت ویرایش شد ';
            }
                else
                $message['error'] = 'مجددا تلاش کنید ';
            return redirect()->route('admin.category')->with($message);

        }

        function deleteCategory($category_id)
        {
            if (Subcategory::where('category_id', $category_id)->count() > 0) {
                $message['error'] = 'این دسته بندی زیر دسته بندی دارد';
                return redirect()->back()->with($message);
            }
            if (Category::destroy($category_id)){
                if (file_exists((public_path('images/icons') . '/' . $category_id) . '.png')) unlink((public_path('images/icons') . '/' . $category_id) . '.png');
                if (file_exists((public_path('images/icons') . '/' . $category_id) . '.jpg')) unlink((public_path('images/icons') . '/' . $category_id) . '.jpg');
                if (file_exists((public_path('images/icons') . '/' . $category_id) . '.png')) unlink((public_path('images/icons') . '/' . $category_id) . '.png');
                $message['success'] = 'دسته بندی با موفقیت جذف شد ';

            }
            else
                $message['error'] = 'مجددا تلاش کنید ';

            return redirect()->back()->with($message);
        }


}
