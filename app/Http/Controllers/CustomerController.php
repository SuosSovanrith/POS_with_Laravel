<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerModel;

class CustomerController extends Controller
{
    //
    public function CustomerView(){
        $result = CustomerModel::orderBy('customer.customer_id', 'desc')->paginate(5);

        return view('admin.customer', ['customers'=>$result]);
    }

    public function AddCustomer(Request $rq){

        $result = new CustomerModel();

        $result->customer_Name = $rq->Name;
        $result->customer_Email = $rq->Email;
        $result->Phone_number = $rq->Phone;
        $result->Address = $rq->Address;
        
        $result->save();

        if (isset($result)){
            session(['message'=>'Customer added successfully!', 'type'=>'success']);
            return redirect('/admin/customer');
        }else{
            session(['message'=>'Customer failed to be added!', 'type'=>'danger']);
            return redirect('/admin/customer');
        }
    }

    // Update
    public function UpdateCustomer(Request $rq){

        $result = CustomerModel::Find($rq->Id);

        $result->customer_Name = $rq->Name;
        $result->customer_Email = $rq->Email;
        $result->Phone_number = $rq->Phone;
        $result->Address = $rq->Address;

        if($rq->hasfile('Photo')){
            $NewPhoto=$rq->file('Photo')->getClientOriginalName();
            $rq->Photo->move(public_path('assets/images/customer/'), $NewPhoto);
            
            $result->Photo = 'assets/images/customer/'.$NewPhoto;
            
        } //when update image, if no image is chosen, then don't update

        $result->save();

        if (isset($result)){
            session(['message'=>'Customer updated successfully!', 'type'=>'success']);
            return redirect('/admin/customer');
        }else{
            session(['message'=>'Customer failed to be updated!', 'type'=>'danger']);
            return redirect('/admin/customer');
        }
    }

    // Delete
    public function DeleteCustomer(Request $rq){
        $deleted = CustomerModel::find($rq->id);
        $deleted->delete();

        if (isset($deleted)){
            session(['message'=>'Customer deleted successfully!', 'type'=>'success']);
        }else{
            session(['message'=>'Customer failed to be deleted!', 'type'=>'danger']);
        }
    }
}
