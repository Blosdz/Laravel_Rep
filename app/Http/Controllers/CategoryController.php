<?php

namespace App\Http\Controllers;


use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Category;

class CategoryController extends Controller
{
    
    public function index(){
        $categories = Category::all();
        return response()->json([
            'code' => 200,
            'status' => 'succes',
            'categories'=>$categories
        ]);
    }
}
