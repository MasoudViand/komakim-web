<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Subcategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
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

    public function index(){
        $categories =Category::orderBy('order', 'desc')->paginate(15);
        $data['categories']=$categories;
        $data['total_count']=Category::count();

        return view('admin.pages.category.listCategory')->with($data);;

    }
    public function addCategoryForm()
    {
        return view('admin.pages.category.addCategory');

    }
     function addCategory(Request $request)
    {
        $this->validate($request,[
            'nameCategory' => 'required',
            'statusCategory' => 'required',
            'orderCategory' => 'required|numeric',
        ]);


        if (Category::where('order',(int)$request['orderCategory'])->first())
        {
            $message['error'] = 'این الویت نمایش وجود دارد ';
            return redirect()->back()->with($message);
        }

        $category = new Category();
        $category->name = $request['nameCategory'];
        $category->status = $request['statusCategory'] === 'true'? true: false;
        $category->order = (int)$request['orderCategory'];

        if ($category->save())
            $message['success'] = 'دسته بندی با موفقیت اضافه شد ';
        else
            $message['error'] = 'مجددا تلاش کنید ';

        return redirect()->back()->with($message);






    }
     function showEditCategoryForm($category_id)
    {
        $category =Category::find($category_id);
        $data['category']= $category;
        return view('admin.pages.category.editCategory')->with($data);
    }
     function editCategory(Request $request)
    {
        $this->validate($request,[
            'nameCategory' => 'required',
            'statusCategory' => 'required',
            'orderCategory' => 'required|numeric',
        ]);


            $category = Category::where('order',(int)$request['orderCategory'])->first();

            if ($category)
            {
                if (!($category->id==$request['idCategory']))
                {
                    $message['error'] = 'این الویت نمایش وجود دارد ';
                    return redirect()->back()->with($message);
                }
            }


                $category = Category::find($request['idCategory']);
                $category->name = $request['nameCategory'];
                $category->status = $request['statusCategory'] === 'true'? true: false;
                $category->order = (int)$request['orderCategory'];

                if ($category->save())
                    $message['success'] = 'دسته بندی با موفقیت ویرایش شد ';
                else
                    $message['error'] = 'مجددا تلاش کنید ';
                return redirect()->route('admin.category')->with($message);

    }
     function deleteCategory ($category_id)
    {
        if (Subcategory::where('category_id',$category_id)->count()>0){
            $message['error'] = 'این دسته بندی زیر دسته بندی دارد';
            return redirect()->back()->with($message);
        }
        if (Category::destroy($category_id))
            $message['success'] = 'دسته بندی با موفقیت جذف شد ';
        else
            $message['error'] = 'مجددا تلاش کنید ';

        return redirect()->back()->with($message);
    }
}
