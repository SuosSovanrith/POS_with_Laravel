<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    //
    public Function Login(Request $rq){
        $Email = $rq->input('Email');
        $Password = $rq->input('Password');

        $result = DB::table('users')
        ->where('email', '=', $Email)
        ->where('password', '=', $Password)
        ->first();

        if(isset($result)){
            $id = $result->id;
            $name = $result->name;
            $email = $result->email;

            session(['auth'=>true, 'id'=>$id, 'name'=>$name, 'email'=>$email]);
            session(['message'=>'Login Successful!  Welcome Back, ' . $name . '.', 'type'=>'success']);

            return redirect('/admin/index');
        }else{

            session(['message'=>'Login Failed!  Please try again.', 'type'=>'danger']);
            
            return redirect('/auth/login');
        }
    }

    public function Logout(Request $rq){
        $rq->session()->flush();
        session(['message'=>'Logout Successful!  See You Later.', 'type'=>'success']);
        
        return redirect('/auth/login');
    }
}
