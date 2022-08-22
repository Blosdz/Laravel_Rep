<?php

namespace Illuminate\Http;
namespace App\Http\Controllers;
// use App\Http\Controllers\Request;
// use App\Models\User;
use App\Package;
use Illuminate\Http\Request;
use DB ;
use App\Models\User;
use App\Role;
use Auth;

class UserController extends Controller{
    //
    public function pruebas(Request $request){
        return "accion de pruebas User controller";
    }
    public function register(Request $request){

        //formato mostrar nombre y apellido cuando se le pide
        // $name=$request->input('name');
        // $surname=$request->input('surname');
        // return"ACCION DE REGISTRO DE USUARIO:$name  $surname";//apirest controladores que devuelvan algo 


        //recoger datos del usuario por post
        $json= $request->input('json',null);

         $params = json_decode($json);
        
        $params_array = json_decode($json,true);
        
//        var_dump($params_array);\
//        
//        
        //validar datos
        $validate = \Validator::make($params_array,[
           'name'=>'required|alpha',
            'surname'=>'required|alpha',
            'email'=>'required|email|unique:users',
            'password'=>'required'
        ]);
        
        if($validate->fails()){
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message'=> 'el usuario no se a creado correctamente',
                'errors'=>$validate->errors()
                );
        }else{           
            $pwd= hash('sha256',$params->password);
            $user = new User();
            $user->name=$params_array['name'];
            $user->surname =$params_array['surname'];
            $user->email =$params_array['email'];
            $user->password =$pwd;
            $user->save();
            $data = array(
               'status' => 'success',
               'code' => 200,
               'message'=> 'el usuario se a creado correctamente',
               'user' => $user
            );
 
        }
        //cifrar la password

        //comprobar si el usuario existe

        //crear usuario


//        $data = array(
//            'status' => 'error',
//            'code' => 404,
//            'message'=> 'el usuario no se a creado correctamente'
//        );
//
        return response()->json($data, $data['code']);

    }
    public function login(Request $request){
        $jwdtAuth = new \App\helpers\JwdtAuth();

        //recibir datos por post
        $json= $request->input('json',null);
        $params=json_decode($json);
        $params_array = json_decode($json,true);

        //validar los datos 
        $validate = \Validator::make($params_array,[
             'email'=>'required|email',
             'password'=>'required'
         ]);
         
         if($validate->fails()){
             $signup = array(
                 'status' => 'error',
                 'code' => 404,
                 'message'=> 'el usuario no se a podido identificar',
                 'errors'=>$validate->errors()
                 );
         }else{
        //cifrar la contraseña
            $pwd= hash('sha256',$params->password);
            $signup=$jwdtAuth->signup($params->email,$pwd);
        //devolver token o datos
            if(!empty($params->gettoken)){
                $signup = $jwdtAuth->signup($params->email,$pwd,true);
            }
        } 

        return response()->json($signup,200); //toke o error
    }
    
    public function update(Request $request){
        $token=$request->header('Authorization');
        $jwdtAuth= new \App\helpers\JwdtAuth();
        $checktoken=$jwdtAuth->checkToken($token);
        if($checktoken){
            echo"<h1>login correcto</h1>";
        }
        else{
            echo"<h1>Login incorrecto</h1>";
        }
        die();
    }
}
