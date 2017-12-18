<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Service;
use App\Subcategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubCategoryController extends Controller
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

        $subcategories =Subcategory::orderBy('order', 'desc')->get();
        $subcategoryArr=[];

        foreach ($subcategories as $subcategory)
        {
            $item=[];
            $item['subcategoryName']= $subcategory->name;
            $item['categoryName'] = Category::find($subcategory->category_id)->name;
            $item['subcategoryOrder'] = $subcategory->order;
            $item['subcategoryId'] = $subcategory->id;
            array_push($subcategoryArr,$item);
        }

        $data['subcategoryArr']=$subcategoryArr;

        return view('admin.pages.subcategory.listsubCategory')->with($data);


    }
    public function addSubCategoryForm()
    {
        $categories = Category::all();
        $data['categories']=$categories;
        return view('admin.pages.subcategory.addSubcategory')->with($data);
    }
    public function addSubCategory(Request $request)
    {
        $this->validate($request,[
            'nameSubCategory' => 'required',
            'orderSubCategory' => 'required|numeric',
        ]);



        if (Subcategory::where('category_id' ,$request['idSubCategory'])->where('order',(int)$request['orderSubCategory'])->count()>0)
        {
            $message['error'] = 'این الویت نمایش  قبلا انتخاب شده ';
            return redirect()->back()->with($message);
        }
        $subcategory = new Subcategory();
        $subcategory->name =$request['nameSubCategory'];
        $subcategory->category_id =$request['idSubCategory'];
        $subcategory->order =(int)$request['orderSubCategory'];

        if ($subcategory->save())
            $message['success'] = 'دسته بندی با موفقیت اضافه شد ';
        else
            $message['error'] = 'مجددا تلاش کنید ';

        return redirect()->back()->with($message);

    }
    function showEditSubCategoryForm($subCategory_id)
    {
        $subCategory = Subcategory::find($subCategory_id);
        $categories = Category::all();

        $data['subCategory']=$subCategory;
        $data['categories']=$categories;

        return view('admin.pages.subcategory.editsubcategory')->with($data);


    }
    function editSubCategory(Request $request)
    {
        $this->validate($request,[
            'nameSubCategory' => 'required',
            'orderSubCategory' => 'required|numeric',
        ]);

        $subCategory = Subcategory::where('category_id',$request['idCategory'])->where('order',(int)$request['orderSubCategory'])->first();

        if ($subCategory )
        {
            if (!($subCategory->id ==$request['idSubCategory'])){

                $message['error'] = 'این الویت نمایش وجود دارد ';
                return redirect()->back()->with($message);

            }
        }
            $subCategory = Subcategory::find($request['idSubCategory']);
            $subCategory->category_id=$request['idCategory'];
            $subCategory->name =$request['nameSubCategory'];
            $subCategory->order =(int)$request['orderSubCategory'];

            if ($subCategory->save())
                $message['success'] = 'دسته بندی با موفقیت ویرایش شد ';
            else
                $message['error'] = 'مجددا تلاش کنید ';
            return redirect()->route('admin.subcategory')->with($message);
    }

    function deleteSubCategory($subCategory_id)
    {
        if (Service::where('subcategory_id',$subCategory_id)->count()>0){

            $message['error'] = 'این زیر دسته بندی سرویش دارد';
            return redirect()->back()->with($message);

        }

        if (Subcategory::destroy($subCategory_id))
        {
            $message['success'] = 'زیر دسته بندی با موفقیت حذف شد ';
        }else{
            $message['error'] = 'مجددا تلاش کنید ';

        }
        return redirect()->back()->with($message);


    }



}
