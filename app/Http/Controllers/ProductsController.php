<?php

namespace App\Http\Controllers;

use App\Models\ProductsModel;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function ProductsView(){
        // $position = PositionModel::all();
        $result = ProductsModel::paginate(10);
        // $result = DB::table('users')->paginate(10);

        return view('admin.products', ['products'=>$result]);
    }

    public function AddProduct(Request $rq){

        $result = new ProductsModel();

        $result->Product_Name = $rq->Product_Name;
        $result->Category_Id = $rq->Category_Id;
        $result->Supplier_Id = $rq->Supplier_Id;
        $result->Quantity = $rq->Quantity;
        $result->Price_In = $rq->Price_In;
        $result->Price_Out = $rq->Price_Out;
        $result->Barcode = $rq->Barcode;
        $result->In_Stock = $rq->In_Stock;

        if($rq->hasfile('Image')){
            $NewImage=$rq->file('Image')->getClientOriginalName();
            $rq->Image->move(public_path('images/products/'), $NewImage);
            
            $result->Image = 'images/products/'.$NewImage;
            
        }else{
            $result->Image = "";
        }
        
        $result->save();

        if (isset($result)){
            session(['message'=>'Product added successfully!', 'type'=>'success']);
            return redirect('/admin/products');
        }else{
            session(['message'=>'Product failed to be added!', 'type'=>'danger']);
            return redirect('/admin/products');
        }
    }

    // Update
    public function UpdateProduct(Request $rq){

        $result = ProductsModel::Find($rq->Id);

        $result->Name = $rq->Name;
        $result->Email = $rq->Email;
        if($rq->Password!=""){
            $result->Password = $rq->Password;
        }
        $result->Phone_number = $rq->Phone;
        $result->Address = $rq->Address;
        $result->Position_id = $rq->Position_Id;

        if($rq->hasfile('Image')){
            $NewImage=$rq->file('Image')->getClientOriginalName();
            $rq->Image->move(public_path('images/products/'), $NewImage);
            
            $result->Image = 'images/products/'.$NewImage;
            
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
    public function DeleteProduct(Request $rq){
        $deleted = ProductsModel::find($rq->id);
        $deleted->delete();

        if (isset($deleted)){
            session(['message'=>'User deleted successfully!', 'type'=>'success']);
        }else{
            session(['message'=>'User failed to be deleted!', 'type'=>'danger']);
        }
    }
}
