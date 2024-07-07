<?php

namespace App\Http\Controllers;

use App\Models\ProductsModel;
use App\Models\CategoryModel;
use App\Models\SupplierModel;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use App\Exports\ProductsExport;
use Picqer;
use Illuminate\Support\Carbon;

class ProductsController extends Controller
{
    public function ProductsView(){

        $category = CategoryModel::all();
        $supplier = SupplierModel::all();
        $result = ProductsModel::Join('category', 'products.category_id', '=', 'category.category_id')
        ->Join('supplier', 'products.supplier_id', '=', 'supplier.supplier_id')
        ->orderBy('products.product_id', 'desc')->paginate(10);

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

    // Get Barcode Image
    public function GetBarcodeImage($barcode){
        $barcodeImage = ProductsModel::select(['barcode_image', 'product_id', 'barcode', 'product_name'])->where('barcode', $barcode)->first();
        return $barcodeImage;
    }

    // Import Product
    public function ImportExcel(Request $rq){
        Excel::import(new ProductsImport, $rq->file('Excel_File'));
        return redirect('/admin/products');
    }

    // Export Product
    public function ExportExcel(){
        return Excel::download(new ProductsExport, 'products.xlsx');
    }

    // Search Product
    public function SearchProduct(Request $rq){
        
        // Search product
        $search = $rq->input('Product_Search');
        $searchproducts = ProductsModel::where('product_name', 'LIKE', '%'.$search.'%')->join('category', 'products.category_id', '=', 'category.category_id')
        ->Join('supplier', 'products.supplier_id', '=', 'supplier.supplier_id')
        ->orderBy('products.product_id', 'desc')->paginate(10);

        $category = CategoryModel::all();
        $supplier = SupplierModel::all();

        return view('admin.products', ['products'=>$searchproducts, 'category'=>$category, 'supplier'=>$supplier]);
    }

    // Filter Product
    public function FilterProduct(Request $rq){

        if($rq->Filter_Period == "" && $rq->Filter_Category == "" && $rq->Filter_Supplier == "" && $rq->Filter_Stock == ""){
            return redirect('/admin/products');
        }

        $category = CategoryModel::all();
        $supplier = SupplierModel::all();

        $result = ProductsModel::Join('category', 'products.category_id', '=', 'category.category_id')
        ->Join('supplier', 'products.supplier_id', '=', 'supplier.supplier_id');
           

        // Filter period
        if($rq->Filter_Period == "Today"){
            $result = $result->whereDate('products.updated_at', '>=', Carbon::today());
        }elseif($rq->Filter_Period == "Yesterday"){
            $result = $result->whereDate('products.updated_at', '>=', Carbon::yesterday());
        }

        if($rq->Filter_Period == "This Week"){
            $result = $result->whereBetween('products.updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);       
        }elseif($rq->Filter_Period == "Last Week"){
            $result = $result->whereBetween('products.updated_at', [Carbon::now()->subWeek(), Carbon::now()]);
        }

        if($rq->Filter_Period == "This Month"){
            $result = $result->whereMonth('products.updated_at', Carbon::now()->month);       
        }elseif($rq->Filter_Period == "Last Month"){
            $result = $result->whereMonth('products.updated_at', Carbon::now()->subMonth()->month);   
        }
        
        if($rq->Filter_Period == "This Year"){
            $result = $result->whereYear('products.updated_at', Carbon::now()->year);  
        }elseif($rq->Filter_Period == "Last Year"){
            $result = $result->whereMonth('products.updated_at', Carbon::now()->subYear()->year);   
        }

        // Filter Category
        if($rq->Filter_Category != ""){
            $result = $result->where('products.category_id', '=', $rq->Filter_Category);
        }

        // Filter Supplier
        if($rq->Filter_Supplier != ""){
            $result = $result->where('products.supplier_id', '=', $rq->Filter_Supplier);
        }

        // Filter Stock
        if($rq->Filter_Stock != ""){
            if($rq->Filter_Stock == "Low Stock"){
                $result = $result->where('products.quantity', '<=', 5)->where('products.quantity', '>', 0);
            }else if($rq->Filter_Stock == "Out of Stock"){
                $result = $result->where('products.quantity', '<', 1);
            }else if($rq->Filter_Stock == "In Stock"){
                $result = $result->where('products.quantity', '>', 0);
            }else if($rq->Filter_Stock == "Descending"){
                $result = $result->orderBy('products.quantity', 'desc');
            }else if($rq->Filter_Stock == "Ascending"){
                $result = $result->orderBy('products.quantity', 'asc');
            }
        }else{
            $result = $result->orderBy('products.created_at');
        }

        $result = $result->paginate(10);

        return view('admin.products', ['products'=>$result, 'category'=>$category, 'supplier'=>$supplier, 'filter_period'=>$rq->Filter_Period, 'filter_category'=>$rq->Filter_Category, 'filter_supplier'=>$rq->Filter_Supplier,  'filter_stock'=>$rq->Filter_Stock]);
    }
}
