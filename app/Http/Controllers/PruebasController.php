<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;    
use App\Models\Category;

class PruebasController extends Controller
{
    //
    public function index(){
        echo' this arr';
    }
    public function testOrm(){
        $posts=Post::all(); //raiz de objetos de todos los datos de Post
        // foreach($posts as $post){
        //     echo "<h1>".$post->title."</h1>";
        //     echo "<p>{$post->user->name}</p>";
        //     echo "<p>".$post->content."</p>";
        //     echo "<hr>";
        // }
        $categories=Category::all();
        foreach($categories as $category){
            echo"<h1>{$category->name}</h1>";
                foreach($category->posts as $post){
                    echo"<h1>".$post->title."</h1>";
                    echo"<span>{$post->user->name} - {$post->category->name}</span";
                    echo"<p>".$post->content."</p>";
                }
                echo"<hr>";
        }
        die();
    }
}

