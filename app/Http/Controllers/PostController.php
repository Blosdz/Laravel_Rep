<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;

class PostController extends Controller
{
    //
    // public function pruebas(Request $request){
    //     return "accion de post controller";
    // }
    public function __construct(){
        $this->middleware('api.auth'/*pide le header y el token del usuario */,['except'=>['index','show']]);
    }

    public function index(){
        $posts = Post::all()->load('category');
        return response()->json([
            'code'=>200,
            'status'=>'success',
            'post'=>$posts
        ],200);
    }
    
    public function show($id){
        $post = Post::find($id)->load('category'); //orm la categoria  ala que pertenece
        if(is_object($post)){
            $data=[
                'code'=>200,
                'status'=>'success',
                'post'=>$post
            ];
        }else{
            $data=[
                'code'=>400,
                'status'=>'error',
                'post'=>'not exist entry'
            ];
        }
        return response()->json($data,$data['code']);
    }
}
