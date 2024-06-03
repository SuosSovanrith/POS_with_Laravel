<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserCartModel;
use App\Models\CustomerModel;
use App\Models\User;

class CartController extends Controller
{
    public function CartView(){

        $customer = CustomerModel::all();
        $user_id = session('user_id');

        $cart = UserCartModel::join('users', 'user_cart.user_id', '=', 'users.user_id')
        ->join('products', 'user_cart.product_id', '=', 'products.product_id')
        ->select('user_cart.quantity as quantity', 'products.product_name', 'products.price_out', 'products.product_name')
        ->where('users.user_id', '=', $user_id)
        ->get();


        return view('admin.cart', ['cart'=>$cart, 'customer'=>$customer]);
    }
}
