<?php

namespace App\Http\Controllers;


use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    //cargar constructor para usar la autenticacion o del middleware ya creado
    public function __construct(){
        $this->middleware('api.auth',['except'=>['index','show']]);
    }


    public function index(){
        $categories = Category::all();
        return response()->json([
            'code' => 200,
            'status' => 'succes',
            'categories'=>$categories
        ]);
    }
    public function show($id){
        $category=Category::find($id);
        if(is_object($category)){
            $data=[
                'code' => 200,
                'status' => 'succes',
                'categories'=>$category
            ];
        }else{
            $data=[
                'code' => 400,
                'status' => 'error',
                'categories'=> 'not found'
            ];
        }
        return response()->json($data,$data['code']);
    }

    public function store(Request $request){
        //recoger los datos por post
        $json=$request->input('json',null);
        $params_array=json_decode($json,true);

        if(!empty($params_array)){
        //validar datos
            $validate=\Validator::make($params_array,[
                'name'=>'required'
            ]);

            //guardar categoria
            if($validate->fails()){
                $data=[
                    'code'=>400,
                    'status'=>'error',
                    'message'=>'no se ha guardado la categoria'
                ];
            }else{
                $category=new Category();
                $category->name=$params_array['name'];
                $category->save();
                $data=[
                    'code'=>200,
                    'status'=>'success',
                    'category'=>$category
                ];
            }
        }
        else{
                 $data=[
                    'code'=>400,
                    'status'=>'error',
                    'message'=>'no se ah enviado ninguna categoria'
                ];       
        }
        //devolver resultados
        return response()->json($data,$data['code']);
    }

    public function update($id,Request $request){
        //recoger datos por post

        //validar los datos

        //quitar lo que no quiero actualizar

        //actualizar el registro (categoria)
        
        //devolver respuesta
        
    }
}
