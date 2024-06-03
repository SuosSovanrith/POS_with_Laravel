<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\PositionModel;

class UsersController extends Controller
{
    //
    public function UsersView(){
        $position = PositionModel::all();
        $result = User::join('position', 'users.position_id', '=', 'position.position_id')->orderBy('users.user_id', 'desc')->paginate(5);

        return view('admin.users', ['users'=>$result, 'position'=>$position]);
    }

    public function AddUser(Request $rq){

        $result = new User();

        $result->Name = $rq->Name;
        $result->Email = $rq->Email;
        $result->Password = $rq->Password;
        $result->Phone_number = $rq->Phone;
        $result->Address = $rq->Address;
        $result->Position_id = $rq->Position_Id;

        if($rq->hasfile('Photo')){
            $NewPhoto=$rq->file('Photo')->getClientOriginalName();
            $rq->Photo->move(public_path('assets/images/users/'), $NewPhoto);
            
            $result->Photo = 'assets/images/users/'.$NewPhoto;
            
        }else{
            $result->Photo = "";
        }
        
        $result->save();

        if (isset($result)){
            session(['message'=>'User added successfully!', 'type'=>'success']);
            return redirect('/admin/users');
        }else{
            session(['message'=>'User failed to be added!', 'type'=>'danger']);
            return redirect('/admin/users');
        }
    }

    // Update
    public function UpdateUser(Request $rq){

        $result = User::Find($rq->Id);

        $result->Name = $rq->Name;
        $result->Email = $rq->Email;
        if($rq->Password!=""){
            $result->Password = $rq->Password;
        }
        $result->Phone_number = $rq->Phone;
        $result->Address = $rq->Address;
        $result->Position_id = $rq->Position_Id;

        if($rq->hasfile('Photo')){
            $NewPhoto=$rq->file('Photo')->getClientOriginalName();
            $rq->Photo->move(public_path('assets/images/users/'), $NewPhoto);
            
            $result->Photo = 'assets/images/users/'.$NewPhoto;
            
        } //when update image, if no image us chosen, then don't update

        $result->save();

        if (isset($result)){
            session(['message'=>'User updated successfully!', 'type'=>'success']);
            return redirect('/admin/users');
        }else{
            session(['message'=>'User failed to be updated!', 'type'=>'danger']);
            return redirect('/admin/users');
        }
    }

    // Delete
    public function DeleteUser(Request $rq){
        $deleted = User::find($rq->id);
        $deleted->delete();

        if (isset($deleted)){
            session(['message'=>'User deleted successfully!', 'type'=>'success']);
        }else{
            session(['message'=>'User failed to be deleted!', 'type'=>'danger']);
        }
    }
}
