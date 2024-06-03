<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartModel;
use App\Models\CustomerModel;

class CartController extends Controller
{
    public function CartView(){
        $Customer = CustomerModel::all();

        return view('admin.cart', ['customer'=>$Customer]);
    }
}
