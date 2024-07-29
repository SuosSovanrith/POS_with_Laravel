<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductsModel;
use App\Models\CategoryModel;

class ShopController extends Controller
{
    public function ProductsView(){

        $category = CategoryModel::all();
        $result = ProductsModel::Join('category', 'products.category_id', '=', 'category.category_id')
        ->Join('supplier', 'products.supplier_id', '=', 'supplier.supplier_id')
        ->orderBy('products.product_id', 'desc')->paginate(10);

        return view('ecommerce.shop', ['products'=>$result, 'category'=>$category]);
    }

}
