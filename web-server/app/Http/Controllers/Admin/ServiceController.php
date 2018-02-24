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
        $this->middleware(['auth:admin','admin']);
    }

    public function index(Request $request)
    {

        $service = new Service();
        $service = $service->newQuery();
        $subcategories=null;
        $subcategories_id_arr = [];
        $queryParam=[];
        if ($request->has('category_id')) {

            $queryParam['category_id']=$request->input('category_id');
            $categoryQueryParam= Category::find($request->input('category_id'));
            $queryParam['category_name']=$categoryQueryParam->name;
            $subcategories = Subcategory::where('category_id', $request->input('category_id'))->get();
            $data['subcategories']=$subcategories;
            foreach ($subcategories as $subcategory) {
                $subcategories_id_arr[] = new ObjectID($subcategory->id);
            }

        }

        if ($request->has('subcategory_id')) {
            $queryParam['subcategory_id']=$request->input('subcategory_id');
            $subcategoriesQueryParam = Subcategory::find($request->input('subcategory_id'));
            $queryParam['subcategory_name']=$subcategoriesQueryParam->name;

            $subcategories_id_arr = [];
            $subcategories_id_arr[] = new ObjectID($request->input('subcategory_id'));

        }


        if (count($subcategories_id_arr) > 0) {
            $service->whereIn('subcategory_id', $subcategories_id_arr);

        }

        if ($request->has('service_name'))
        {
            $queryParam['service_name']=$request->input('service_name');

            //dd('%'.$request->input('name_service').'%');
            $service->where('name', 'like', '%'.$request->input('service_name').'%');
        }




        $services = $service->paginate(15);





        $serviceArr=[];

        foreach ($services as $service)
        {
            $item =[];

            $item['serviceName']=$service->name;
            $item['servicePrice'] =$service->price;
            $item['serviceCommission'] =$service->commission;
            $item['serviceUnit'] =$service->unit;
            $item['serviceDescription'] =$service->description;
            $item['serviceMinimumNumber'] =$service->minimum_number;
            $item['id'] =$service->_id;

            $subcategory = Subcategory::find($service->subcategory_id);
            $item['serviceSubCategoryName'] =$subcategory->name;
            $item['serviceCategoryName'] =Category::find($subcategory->category_id)->name;

            array_push($serviceArr,$item);

        }

        $total_count=Service::count();
        $categories = Category::all();

        $data['categories']=$categories;

        $data['serviceArr']=$serviceArr;
        $data['services']=$services;
        $data['total_count']=$total_count;
        $data['page_title']='لیست سرویس ها';
        $data['queryParam']=$queryParam;
        $data['subcategories']=$subcategories;






        return view('admin.pages.service.services')->with($data);;
    }
    public function addServiceForm()
    {
        $categoris =Category::all();
        $data['categoris']=$categoris;
        $data['page_title']='افزودن سرویس';

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
            'unitService' => 'required'
        ]);

        


       $service = new Service();
       $service->name=$request['nameService'];
       $service->subcategory_id=new ObjectID($request['subcategory']);
       $service->price=(int)$request['priceService'];
       $service->unit=$request['unitService'];
       $service->minimum_number=(int)$request['minOrderService']?:1;
       if (!is_null($request['descService']))
           $service->description = $request['descService'];
        if (!is_null($request['commissionService']))
            $service->commission = (int)$request['commissionService'];

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

        $data['service']=       $service;
        $data['subcategory']=   $subcategory;
        $data['category']=      $category;
        $data['subcategories']= $subcategories;
        $data['categories']=    $categories;
        $data['questions']=     $questions;
        $data['page_title']=    'ویرایش سرویس';



        return view('admin.pages.service.editService')->with($data);

    }

    public function editService(Request $request)
    {

        $this->validate($request,[
            'nameService' => 'required',
            'subcategory' => 'required',
            'priceService' => 'required|numeric',
            'minOrderService' => 'required|numeric',
            'unitService' => 'required'
        ]);

        $service =Service::find($request['idService']);
        $service->name =            $request['nameService'];
        $service->price =          (int) $request['priceService'];
        $service->minimum_number = (int) $request['minOrderService'];
        if (!is_null($request['commissionService']))
            $service->commission =  (int)$request['commissionService'];
        else
            $service->commission=null;


        $service->subcategory_id = new ObjectID( $request['subcategory']);
        $service->description =     $request['descService'];
        $service->unit =            $request['unitService'];

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
        $servicQuestion->service_id =   $request['idService'];
        $servicQuestion->questions  =   $request['questionService'];

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
