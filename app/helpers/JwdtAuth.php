<?php

namespace App\helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\User;


class JwdtAuth{
    public $key;
    
    public function __construct(){
        $this->key='clave_secreta-990'; //key para generar el token 
    }

    public function signup($email, $password,$getToken=NULL){
        //buscar si el usuario existe con sus credenciales
        $user =  User::where([
            'email'=>$email,
            'password'=>$password
        ])->first();//te saca unicamente un objeto tmb get o all que te saca todo los usuarios 

        //comprobar si son correctas (objeto)
        $signup = false;
        if(is_object($user)){
            $signup=true; 
        }
        //generar el token con los datos del usuario identificado
        if($signup){
            $token=array(
                'sub'=>$user->id,
                'email'=>$user->email,
                'name'=>$user->name,
                'surname'=>$user->surname,
                'iat'=>time(),//tiempo generado del token
                'ext'=>time()+(7*24*60*60)//cuando se caduca el token en este caso una semana 
            );
            $jwt=JWT::encode($token,$this->key,/*algoritmo de codificacion*/'HS256');
            $decode=JWT::decode($jwt,$this->key,['HS256']);
            if(is_null($getToken)){
                $data = $jwt;
            }else{
                $data = $decode;
            }

        }else{
            $data=array(
                'status'=>'error',
                'message'=>'login incorrecto'
            );
        }
        //devolver los datos decodificados con el token, en funcion de un parametro

        return $data;
    }
    
    public function checkToken($jwt,$getIdentify=false){
        $auth=false;
        try{
            
            $decode=JWT::decode($jwt,$this->key,['HS256']);
        }catch(\UnexpectedValueException $e){
            $auth=false;
        }catch(\DomainException $e){
            $auth=false;
        }
        if(!empty($decode)&&is_object($decode)&& isset($decode->sub)){
            $auth=true;
        }else{
            $auth=false;
        }
        if($getIdentify){
            return $decode;
        }
            
        return $auth;
    }

}