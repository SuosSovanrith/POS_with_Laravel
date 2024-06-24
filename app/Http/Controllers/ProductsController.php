<?php

namespace App\Http\Controllers;

use App\Models\ProductsModel;
use App\Models\CategoryModel;
use App\Models\SupplierModel;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Imports\ProductsImport;
use Excel;
use Picqer;

class ProductsController extends Controller
{
    public function ProductsView(){
        $category = CategoryModel::all();
        $supplier = SupplierModel::all();
        $result = ProductsModel::Join('category', 'products.category_id', '=', 'category.category_id')->Join('supplier', 'products.supplier_id', '=', 'supplier.supplier_id')->orderBy('products.product_id', 'desc')->paginate(5);
        return view('admin.products', ['products'=>$result, 'category'=>$category, 'supplier'=>$supplier]);
    }

    public function GenBarcode(){
        $barcode_rand = rand(1000000,1999999);
        $result = ProductsModel::select('barcode')->get();

        if($result->contains('barcode', $barcode_rand)){
            $this->GenBarcode();
        }

        return $barcode_rand;

    }

    public function AddProduct(Request $rq){
   
        $result = new ProductsModel();

        $result->Product_Name = $rq->Product_Name;
        $result->Category_Id = $rq->Category_Id;
        $result->Supplier_Id = $rq->Supplier_Id;
        $result->Quantity = $rq->Quantity;
        $result->Price_In = $rq->Price_In;
        $result->Price_Out = $rq->Price_Out;

        // Barcode
        if(isset($rq->Barcode)){
            $barcode_rand = $this->GenBarcode();  
            $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
            $barcodeimage = $generator->getBarcode($barcode_rand, $generator::TYPE_CODE_128);

            $result->Barcode = $barcode_rand;
            $result->barcode_image = $barcodeimage;
        }

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

        if(isset($rq->Barcode)){
            $barcode_rand = $this->GenBarcode();  
            $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
            $barcodeimage = $generator->getBarcode($barcode_rand, $generator::TYPE_CODE_128);

            $result->Barcode = $barcode_rand;
            $result->barcode_image = $barcodeimage;
        }

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

    // Import Product
    public function ImportExcel(Request $rq){
        Excel::import(new ProductsImport, $rq->file('Excel_File'));
        return redirect('/admin/products');
    }
}
