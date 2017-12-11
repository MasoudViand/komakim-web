<?php

namespace App\Http\Controllers;

use App\Category;
use App\Subcategory;
use Illuminate\Http\Request;

class AdminController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

//        $subCategory = new Subcategory();
//        $subCategory->category_id ='5a2d5f16978ef44bc4266112';
//        $subCategory->name= 'testsub';
//        $subCategory->order= 2;
//        $subCategory->save();
       $sub= Category::first()->subcategories();
       dd($sub);


//        $category =new Category();
//        $category->name='testname';
//        $category->status =true;
//        $category->order = 4;
//        $category->save();

        return view('admin.pages.dashboard');
    }
}
