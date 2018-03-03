<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Subcategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;

class CategoryController extends Controller
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
        $categories = Category::orderBy('order', 'asc')->paginate(15);


        foreach ($categories as $category) {
            $filepath =  '/images/icons/service-icon-default.jpg';


            if (file_exists((public_path('images/icons') . '/' . $category->id) . '.png')) $filepath = ('/images/icons') . '/' . $category->id . '.png';
            if (file_exists((public_path('images/icons') . '/' . $category->id) . '.jpg')) $filepath = ('/images/icons') . '/' . $category->id . '.jpg';
            if (file_exists((public_path('images/icons') . '/' . $category->id) . '.jpeg')) $filepath = ('/images/icons') . '/' . $category->id . '.jpeg';

            $filepath=URL::to('/') .''.$filepath;

            $category->filepath = $filepath;
        }



            $data['categories'] = $categories;
            $data['total_count'] = Category::count();
            $data['page_title']='لیست دسته بندی ها';


        return view('admin.pages.category.listCategory')->with($data);;

        }

        function addCategoryForm()
        {
            $data['page_title']='افزودن دسته بندی';

            return view('admin.pages.category.addCategory')->with($data);

        }

        function addCategory(Request $request)
        {

            // dd($request['imageّIcon']);
            $this->validate($request, [
                'nameCategory' => 'required',
                'statusCategory' => 'required',
                'orderCategory' => 'required|numeric',
                'imageّIcon' => 'required|image|mimes:jpeg,png,jpg|max:512',
                ]);

            $category = new Category();
            $category->name = $request['nameCategory'];
            $category->status = $request['statusCategory'] === 'true' ? true : false;
            $category->order = (int)$request['orderCategory'];

            if ($category->save()) {
                $imageName = $category->id . '.' . request()->imageّIcon->getClientOriginalExtension();

                if (file_exists((public_path('images/icons') . '/' . $category->id) . '.png')) unlink((public_path('images/icons') . '/' . $category->id) . '.png');
                if (file_exists((public_path('images/icons') . '/' . $category->id) . '.jpg')) unlink((public_path('images/icons') . '/' . $category->id) . '.jpg');
                if (file_exists((public_path('images/icons') . '/' . $category->id) . '.png')) unlink((public_path('images/icons') . '/' . $category->id) . '.jpeg');

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
