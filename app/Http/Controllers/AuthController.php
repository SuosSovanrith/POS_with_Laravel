<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\CustomerModel;

class AuthController extends Controller
{
    //
    public Function Login(Request $rq){
        $Email = $rq->input('Email');
        $Password = $rq->input('Password');

        $auth = DB::table('users')
        ->where('users.email', '=', $Email)
        ->where('users.password', '=', $Password)
        ->first();

        if(isset($auth)){
            $result = DB::table('users')->join('position', 'users.position_id', '=', 'position.position_id')
            ->where('users.email', '=', $Email)
            ->where('users.password', '=', $Password)->first();

            $user_id = $result->user_id;
            $name = $result->name;
            $email = $result->email;
            $photo = $result->photo;
            $position_name = $result->position_name;

            session(['auth'=>true, 'user_id'=>$user_id, 'name'=>$name, 'email'=>$email, 'photo'=>$photo, 'position_name'=>$position_name]);
            session(['message'=>'Login Successful!  Welcome Back, ' . $name . '.', 'type'=>'success']);

            if($position_name == "Customer"){
                return redirect('/ecommerce/shop');

            }else{
                return redirect('/admin/cart');
            }

        }else{

            session(['message'=>'Login Failed!  Please try again.', 'type'=>'danger']);
            
            return redirect('/auth/login');
        }
    }

    public function Register(Request $rq){
        $Name = $rq->input('Name');
        $Email = $rq->input('Email');
        $Password = $rq->input('Password');

        $user = new User();

        $position_id = DB::table('position')->select(['position_id'])->where('position_name', 'Customer')->first();

        $user->name = $Name;
        $user->email = $Email;
        $user->password = $Password;
        $user->position_id = $position_id->position_id;
        $user->save();

        $customer = new CustomerModel();
        $customer->user_id = $user->user_id;
        $customer->customer_name = $Name;
        $customer->customer_email = $Email;
        $customer->save();
        
        session(['message'=>'Register Successful!', 'type'=>'success']);
        return redirect('/auth/login');
    }

    public function Logout(Request $rq){
        $rq->session()->flush();
        session(['message'=>'Logout Successful!  See You Later.', 'type'=>'success']);
        
        return redirect('/auth/login');
    }
}
