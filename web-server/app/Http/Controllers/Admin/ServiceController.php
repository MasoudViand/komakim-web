<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\MailTemplate;
use App\Service;
use App\ServiceQuestion;
use App\Subcategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use MongoDB\BSON\ObjectID;

class ServiceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {


        $services = Service::paginate(15);

        $serviceArr=[];

        foreach ($services as $service)
        {
            $item =[];

            $item['serviceName']=$service->name;
            $item['servicePrice'] =$service->price;
            $item['serviceDescription'] =$service->description;
            $item['serviceMinimumNumber'] =$service->minimum_number;
            $item['id'] =$service->_id;

            $subcategory = Subcategory::find($service->subcategory_id);
            $item['serviceSubCategoryName'] =$subcategory->name;
            $item['serviceCategoryName'] =Category::find($subcategory->category_id)->name;

            array_push($serviceArr,$item);

        }
        //dd($serviceArr);
       $data['serviceArr']=$serviceArr;



        return view('admin.pages.service.services')->with($data);;
    }
    public function addServiceForm()
    {
        $categoris =Category::all();
        $data['categoris']=$categoris;
        return view('admin.pages.service.addService')->with($data);
    }

    public function getSubCategory($category_id)
    {

        $subcategories=Subcategory::where('category_id',$category_id)->get();

        return json_encode($subcategories);

    }
    public function addService(Request $request)
    {

        if(is_null($request['subcategory']))
        {
            $message['error'] = 'زیر دسته باید انتخاب شود';


            return redirect()->back()->with($message);
        }

        $this->validate($request,[
            'nameService' => 'required',
            'subcategory' => 'required',
            'priceService' => 'required|numeric',
            'minOrderService' => 'required|numeric'
        ]);



       $service = new Service();
       $service->name=$request['nameService'];
       $service->subcategory_id=new ObjectID($request['subcategory']);
       $service->price=$request['priceService'];
       $service->minimum_number=$request['minOrderService'];
       if (!is_null($request['descService']))
           $service->description = $request['descService'];
       $service->save();

        $message['success'] = 'سرویس با موفقیت اضافه شد';

       return redirect()->back()->with($message);



    }
    public function showEditServiceForm($service_id)
    {
        $service=Service::find($service_id);
        $questions = ServiceQuestion::where('service_id',$service_id)->get();

        $subcategory = Subcategory::find($service->subcategory_id);

        $category= Category::find($subcategory->category_id);
        $categories=Category::all();

        $subcategories = Subcategory::where('category_id',$category->id)->get();

        $data['service']=$service;
        $data['subcategory']=$subcategory;
        $data['category']=$category;
        $data['subcategories']=$subcategories;
        $data['categories']=$categories;
        $data['questions']=$questions;

        return view('admin.pages.service.editService')->with($data);

    }

    public function editService(Request $request)
    {


        $this->validate($request,[
            'nameService' => 'required',
            'subcategory' => 'required',
            'priceService' => 'required|numeric',
            'minOrderService' => 'required|numeric'
        ]);

        $service =Service::find($request['idService']);
        $service->name = $request['nameService'];
        $service->price = $request['priceService'];
        $service->minimum_number = $request['minOrderService'];
        $service->subcategory_id = $request['subcategory'];
        $service->description = $request['descService'];

        if ($service->save())
        {
            $message['success'] = 'سرویس با موفقیت ویرایش شد';

            return redirect()->route('admin.service')->with($message);
        }else{
            $message['error'] = 'مجددا تلاش کنید';

            return redirect()->back()->with($message);

        }

    }
    public function deleteService($service_id)
    {
        if (Service::destroy($service_id))
            $message['success'] = 'سرویس با موفقیت حذف شد';
        else
            $message['error'] = 'محددا تلاش کنید';
        return redirect()->back()->with($message);
    }

    public function addQuestionService(Request $request)
    {
        $this->validate($request,[
            'questionService' => 'required',
        ]);

        $servicQuestion = new ServiceQuestion();
        $servicQuestion->service_id =$request['idService'];
        $servicQuestion->questions = $request['questionService'];

        if ($servicQuestion->save())
        {
            $message['success'] = 'سرویس با موفقیت ویرایش شد';
        }else
            $message['error'] = 'سرویس با موفقیت ویرایش شد';

        return redirect()->back()->with($message);


    }
    public function deleteQuestionService($question_id)
    {
        if(ServiceQuestion::destroy($question_id))
            $message['success'] = 'سوال با موفقیت حذف شد';
        else
            $message['error'] = 'مجددا تلاش کن';

        return redirect()->back()->with($message);


    }
}