<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;
use App\helpers\JwdtAuth;

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

    public function store(Request $request){
        //recoger datos por post
        $json=$request->input('json',null);
        $params=\json_decode($json);
        $params_array=\json_decode($json,true);
        if(!empty($params_array)){

            //conseguir usuario identificado
            $user=$this->getIdentity($request);
            //validar datos
            $validate= \Validator::make($params_array,[
                'title'=>'required',
                'content'=>'required',
                'category_id'=>'required',
                'image'=>'required'
            ]);
            if($validate->fails()){
                $data=[
                    'code'=>400,
                    'status'=>'not succes',
                    'message'=>'no se guardo el post'
                ];
            }else{
                    $post=new Post();
                    $post->user_id=$user->sub;
                    $post->category_id=$params->category_id;
                    $post->title=$params->title;
                    $post->content=$params->content;
                    $post->image=$params->image;    
                    $post->save();
                    $data=[
                        'code'=>200,
                        'status'=>'succes',
                        'message'=>$post
                    ];
                }
        }else{
            $data=[
                'code'=>400,
                'status'=>'not success',
                'message'=>'envia datos bien'
            ];
        }        
        //devolver respuesta
        return response()->json($data,$data['code']);
    }

    public function update($id, Request $request){
        //recoger datos de post 
        $json=$request->input('json',null);
        $params_array =json_decode($json,true);
        if(!empty($params_array)){
            //validar datos 
            $validate=\Validator::make($params_array,[
                'title'=>'required',
                'content'=>'required',
                'category_id'=>'required'
            ]);
            if($validate->fails()){
                return response()->json($validate->errors(),400);
            }
            //eliminar lo que no se actualiza

            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['user']);
            //comprobacion si es el usuario que hizo el post 
            $user=$this->getIdentity($request);
            
            $where=['id'=>$id,'user_id'=>$user->sub];
            //actualizar el registro 
            $post=Post::updateOrCreate($where,$params_array); //llega por url, sacar registro cuyo id sea este
            //devolver algo
            $data=array(
                'code'=>200 ,
                'status'=>'succes',
                'post'=>$params_array
        );
    }
    else{
            $data=array(
                'code'=>400 ,
                'status'=>fail,
                'post'=>$params_array);
    }
        return response()->json($data,$data['code']);
    }
    public function destroy($id,Request $request){
        //comprobacion si es el usuario que hizo el post 
        $user=$this->getIdentity($request); 
        $post=Post::where('id',$id)->where('user_id',$user->sub)->first();
        if(!empty($post)){
            //conseguir registro 
            $post=Post::find($id);
            //borrarlo
            $post->delete();
            //borrar si existe
            $data=[
                'code'=>200,
                'status'=>'success',
                'post'=>$post
            ];
        }
        else{
            $data=[
                'code'=>400,
                'status'=>'error',
                'message'=>'no existe este post'
            ];
        }
        return response()->json($data,$data['code']);
    }

    private function getIdentity($request){
        $jwtAuth=new JwdtAuth();
        $token=$request->header('Authorization',null);
        $user=$jwtAuth->checkToken($token,true);
        return $user;
    }

}
