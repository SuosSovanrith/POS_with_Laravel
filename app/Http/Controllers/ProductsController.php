<?php

namespace App\Http\Controllers;

use App\Models\ProductsModel;
use App\Models\CategoryModel;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function ProductsView(){
        $category = CategoryModel::all();
        $result = ProductsModel::Join('category', 'products.category_id', '=', 'category.category_id')->orderBy('products.id', 'desc')->paginate(5);

        return view('admin.products', ['products'=>$result, 'category'=>$category]);
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
        if($result->Quantity > 0){
            $result->In_Stock = 1;
        }else{
            $result->In_Stock = 0;
        }

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

        $result->Product_Name = $rq->Product_Name;
        $result->Category_Id = $rq->Category_Id;
        $result->Supplier_Id = $rq->Supplier_Id;
        $result->Quantity = $rq->Quantity;
        $result->Price_In = $rq->Price_In;
        $result->Price_Out = $rq->Price_Out;
        $result->Barcode = $rq->Barcode;
        if($result->Quantity > 0){
            $result->In_Stock = 1;
        }else{
            $result->In_Stock = 0;
        }

        if($rq->hasfile('Image')){
            $NewImage=$rq->file('Image')->getClientOriginalName();
            $rq->Image->move(public_path('images/products/'), $NewImage);
            
            $result->Image = 'images/products/'.$NewImage;
            
        } //when update image, if no image us chosen, then don't update

        $result->save();

        if (isset($result)){
            session(['message'=>'Product updated successfully!', 'type'=>'success']);
            return redirect('/admin/products');
        }else{
            session(['message'=>'Product failed to be updated!', 'type'=>'danger']);
            return redirect('/admin/products');
        }
    }

    // Delete
    public function DeleteProduct(Request $rq){
        $deleted = ProductsModel::find($rq->id);
        $deleted->delete();

        if (isset($deleted)){
            session(['message'=>'Product deleted successfully!', 'type'=>'success']);
        }else{
            session(['message'=>'Product failed to be deleted!', 'type'=>'danger']);
        }
    }

    // View Single Product
    public function ViewProduct(Request $rq){
        $result = ProductsModel::find($rq->id);

        return view('admin.productview', ['products'=>$result]);
    }
}
