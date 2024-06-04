<?php

namespace App\Http\Controllers;

use App\Models\ProductsModel;
use App\Models\CategoryModel;
use App\Models\SupplierModel;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ProductsController extends Controller
{
    public function ProductsView(){
        $category = CategoryModel::all();
        $supplier = SupplierModel::all();
        $result = ProductsModel::Join('category', 'products.category_id', '=', 'category.category_id')->Join('supplier', 'products.supplier_id', '=', 'supplier.supplier_id')->orderBy('products.product_id', 'desc')->paginate(5);
        return view('admin.products', ['products'=>$result, 'category'=>$category, 'supplier'=>$supplier]);
    }

    public function AddProduct(Request $rq){
   
        // Barcode
        $barcode_rand = rand(100000,999999);
        $generatorPNG = new BarcodeGeneratorPNG();
        $barcodeimage = $generatorPNG->getBarcode($barcode_rand, $generatorPNG::TYPE_CODE_128);

        $result = new ProductsModel();

        $result->Product_Name = $rq->Product_Name;
        $result->Category_Id = $rq->Category_Id;
        $result->Supplier_Id = $rq->Supplier_Id;
        $result->Quantity = $rq->Quantity;
        $result->Price_In = $rq->Price_In;
        $result->Price_Out = $rq->Price_Out;
        $result->Barcode = $barcode_rand;

        if($result->Quantity > 0){
            $result->In_Stock = 1;
        }else{
            $result->In_Stock = 0;
        }

        if($rq->hasfile('Image')){
            $NewImage=$rq->file('Image')->getClientOriginalName();
            $rq->Image->move(public_path('assets/images/products/'), $NewImage);
            
            $result->Image = 'assets/images/products/'.$NewImage;
            
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
        // $result->Barcode = $rq->Barcode;

        if($result->Quantity > 0){
            $result->In_Stock = 1;
        }else{
            $result->In_Stock = 0;
        }

        if($rq->hasfile('Image')){
            $NewImage=$rq->file('Image')->getClientOriginalName();
            $rq->Image->move(public_path('assets/images/products/'), $NewImage);
            
            $result->Image = 'assets/images/products/'.$NewImage;
            
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
