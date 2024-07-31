<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductsModel;
use App\Models\CategoryModel;

class ShopController extends Controller
{
    public function ShopView(){

        $category = CategoryModel::all();
        $result = ProductsModel::Join('category', 'products.category_id', '=', 'category.category_id')
        ->Join('supplier', 'products.supplier_id', '=', 'supplier.supplier_id')
        ->orderBy('products.product_id', 'desc')->paginate(24);

        return view('ecommerce.shop', ['products'=>$result, 'category'=>$category]);
    }

    // Search Shop
    public function SearchShop(Request $rq){
        
        $search = $rq->input('Shop_Search');
        $searchproducts = ProductsModel::where('product_name', 'LIKE', '%'.$search.'%')->join('category', 'products.category_id', '=', 'category.category_id')
        ->Join('supplier', 'products.supplier_id', '=', 'supplier.supplier_id')
        ->orderBy('products.product_id', 'desc')->paginate(24);

        $category = CategoryModel::all();

        return view('ecommerce.shop', ['products'=>$searchproducts, 'category'=>$category]);
    }    

    // Filter Shop
    public function FilterShop(Request $rq){

        if($rq->Start_Price == "" && $rq->End_Price == "" && $rq->Filter_Category == ""){
            return redirect('/ecommerce/shop');
        }
    
        $category = CategoryModel::all();
    
        $result = ProductsModel::Join('category', 'products.category_id', '=', 'category.category_id')
        ->Join('supplier', 'products.supplier_id', '=', 'supplier.supplier_id')
        ->orderBy('products.product_id', 'desc');
               
        // Filter Category
        if($rq->Filter_Category != ""){
            $result = $result->where('products.category_id', '=', $rq->Filter_Category);
        }

        // Filter Price
        if($rq->Start_Price != ""){
            $result = $result->where('products.price_out', '>=', $rq->Start_Price);
        }
        if($rq->End_Price != ""){
            $result = $result->where('products.price_out', '<=', $rq->End_Price);
        }
    
        $result = $result->paginate(24);
        
        return view('ecommerce.shop', ['products'=>$result, 'category'=>$category, 'start_price'=>$rq->Start_Price, 'end_price'=>$rq->End_Price]);
    }

}
