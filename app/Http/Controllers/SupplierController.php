<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierModel;

class SupplierController extends Controller
{
    //
    public function SupplierView(){
        $result = SupplierModel::paginate(5);

        return view('admin.supplier', ['suppliers'=>$result]);
    }

    public function AddSupplier(Request $rq){

        $result = new SupplierModel();

        $result->supplier_Name = $rq->Name;
        $result->supplier_Email = $rq->Email;
        $result->Phone_number = $rq->Phone;
        $result->Address = $rq->Address;

        if($rq->hasfile('Photo')){
            $NewPhoto=$rq->file('Photo')->getClientOriginalName();
            $rq->Photo->move(public_path('assets/images/supplier/'), $NewPhoto);
            
            $result->Photo = 'assets/images/supplier/'.$NewPhoto;
            
        }else{
            $result->Photo = "";
        }
        
        $result->save();

        if (isset($result)){
            session(['message'=>'Supplier added successfully!', 'type'=>'success']);
            return redirect('/admin/supplier');
        }else{
            session(['message'=>'Supplier failed to be added!', 'type'=>'danger']);
            return redirect('/admin/supplier');
        }
    }

    // Update
    public function UpdateSupplier(Request $rq){

        $result = SupplierModel::Find($rq->Id);

        $result->supplier_Name = $rq->Name;
        $result->supplier_Email = $rq->Email;
        $result->Phone_number = $rq->Phone;
        $result->Address = $rq->Address;

        if($rq->hasfile('Photo')){
            $NewPhoto=$rq->file('Photo')->getClientOriginalName();
            $rq->Photo->move(public_path('assets/images/supplier/'), $NewPhoto);
            
            $result->Photo = 'assets/images/supplier/'.$NewPhoto;
            
        } //when update image, if no image is chosen, then don't update

        $result->save();

        if (isset($result)){
            session(['message'=>'Supplier updated successfully!', 'type'=>'success']);
            return redirect('/admin/supplier');
        }else{
            session(['message'=>'Supplier failed to be updated!', 'type'=>'danger']);
            return redirect('/admin/supplier');
        }
    }

    // Delete
    public function DeleteSupplier(Request $rq){
        $deleted = SupplierModel::find($rq->id);
        $deleted->delete();

        if (isset($deleted)){
            session(['message'=>'Supplier deleted successfully!', 'type'=>'success']);
        }else{
            session(['message'=>'Supplier failed to be deleted!', 'type'=>'danger']);
        }
    }
}
