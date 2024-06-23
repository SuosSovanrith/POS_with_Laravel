<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthApiController extends Controller
{

    public function checkKey($apikey){
        $keys = array('123456');
        if(in_array($apikey, $keys)){
            return true;
        }else{
            return false;
        } 
    }

    public Function Login(Request $rq){

        if(!$this->checkKey($rq->header('api_key'))){
            $data = [
                'status'=>403,
                'message'=>'Invalid Api Key',
            ];
            return response()->json($data);
            
        }else{

            $Email = $rq->header('Email');
            $Password = $rq->header('Password');

            $result = DB::table('users')->join('position', 'users.position_id', '=', 'position.position_id')
            ->where('users.email', '=', $Email)
            ->where('users.password', '=', $Password)
            ->first();

            if(isset($result)){
                $user_id = $result->user_id;
                $name = $result->name;
                $email = $result->email;
                $photo = $result->photo;
                $position_name = $result->position_name;

                $data = [
                    'status'=>200,
                    'message'=>'Login Successful!  Welcome Back, ' . $name . '.',
                    'user_id'=>$user_id, 'name'=>$name, 'email'=>$email, 'photo'=>$photo, 'position_name'=>$position_name,
                ];

                return response()->json($data);

            }else{

                $data = [
                    'status'=>401,
                    'message'=>'Login Failed!  Please try again.'
                ];

                return response()->json($data);
            }
        }
    }
}
