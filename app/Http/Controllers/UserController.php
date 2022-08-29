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
        //comprobar si esta identificado
        $token=$request->header('Authorization');
        $jwdtAuth= new \App\helpers\JwdtAuth(); 


        //recoger los datos por post 
        //actualizar el usuario
        $json=$request->input('json',null);
        $params_array=\json_decode($json,true);
        $checktoken=$jwdtAuth->checkToken($token);
        if($checktoken && !empty($params_array)){
         
            //sacar usuario identificado
            $user=$jwdtAuth->checkToken($token,true);
            //validar datos
            $validate=\Validator::make($params_array,[
                'name'=>'required|alpha',
                'surname'=>'required|alpha',
                'email'=>'required|email|unique:users'.$user->sub
            ]);

            //quitar los campos que no quiero actualizar
            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);
            //actualizard usuario en bbdd            
            $user_update=User::where('id',$user->sub)->update($params_array);
            //devolver array con resultado
            $data=array(
                'code'=>200,
                'status'=>'succes',
                'user'=>$user,
                'changes'=>$params_array
            );
 

        }
        else{
            $data=array(
                'code'=>400,
                'status'=>'error',
                'message'=>'el usuario no esta identificado'
            );
        }
        return response()->json($data,$data['code']);
    }
    
    public function upload(Request $request){
        //recoger datos de la peticion 
        $image=$request->file('file0');

        //guardar imagen
        if($image){
            $image_name=time().$image->getClientOriginalName();
            \Storage::disk('users')->put($image_name,\File::get($image)/*te consige el fichero y te lo guarda con el metodo put */);//cada disco es una carpeta
            $data=array(
                'code'=>200,
                'status'=>'completed charge',
                'message'=>'succes'
            );
        }else{
            $data=array(
                'code'=>400,
                'status'=>'error',
                'message'=>'el usuario no esta identificado'
            );
        }
            
        //Devolver el resultado
        $data=array(
                'code'=>400,
                'status'=>'error',
                'message'=>'el usuario no esta identificado'
        );
        return response()->json($data,$data['code']);
    }
    
}
