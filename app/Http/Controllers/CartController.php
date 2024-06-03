<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserCartModel;
use App\Models\CustomerModel;
use App\Models\ProductsModel;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function CartView(){

        $customer = CustomerModel::all();
        $products = ProductsModel::all();

        $user_id = session('user_id');

        $cart = UserCartModel::join('users', 'user_cart.user_id', '=', 'users.user_id')
        ->join('products', 'user_cart.product_id', '=', 'products.product_id')
        ->where('users.user_id', '=', $user_id)
        ->get();

        return view('admin.cart', ['cart'=>$cart, 'customer'=>$customer, 'products'=>$products]);
    }

    public function AddCart(Request $rq){
        
        $validator = Validator::make($rq->all(),[
            'Barcode' => 'required|exists:products,barcode'
        ]);

        if ($validator->fails()) {
            session(['message'=>"Invalid Barcode ". $rq->Barcode , 'type'=>'danger']);
        }else{

            $barcode = $rq->Barcode;
            $user_id = session('user_id');
            
            // Check if already have product in cart
            $checkInCart = UserCartModel::join('products', 'user_cart.product_id', '=', 'products.product_id')
            ->where('products.barcode', '=', $barcode)
            ->where('user_cart.user_id', '=', $user_id)
            ->first();
            
            if(isset($checkInCart)){
                $checkInCart->cart_quantity = $checkInCart->cart_quantity + 1;
                $checkInCart->save();

                session(['message'=>"Added 1 ". $checkInCart->product_name . " to cart.", 'type'=>'success']);
            }else{
                $product = ProductsModel::where('barcode', '=', $barcode)->first();
                $result = new UserCartModel();
    
                $result->user_id = $user_id;
                $result->product_id = $product->product_id;
                $result->cart_quantity = 1;
                $result->save();
                
                session(['message'=>"Added ". $product->product_name . " to cart.", 'type'=>'success']);
            }
        }
    }

    public function AddCartImage($Barcode){
        
    $user_id = session('user_id');

    // Check if already have product in cart
    $checkInCart = UserCartModel::join('products', 'user_cart.product_id', '=', 'products.product_id')
    ->where('products.barcode', '=', $Barcode)
    ->where('user_cart.user_id', '=', $user_id)
    ->first();

    if(isset($checkInCart)){
        $checkInCart->cart_quantity = $checkInCart->cart_quantity + 1;
        $checkInCart->save();

        session(['message'=>"Added 1 ". $checkInCart->product_name . " to cart.", 'type'=>'success']);
    }else{
        $product = ProductsModel::where('barcode', '=', $Barcode)->first();
        $result = new UserCartModel();
    
        $result->user_id = $user_id;
        $result->product_id = $product->product_id;
        $result->cart_quantity = 1;
        $result->save();

        session(['message'=>"Added ". $product->product_name . " to cart.", 'type'=>'success']);
    }
        
    return redirect('/admin/cart');

    }

    public function UpdateCartQuantity(Request $rq){
        
        $validator = Validator::make($rq->all(),[
            'Cart_Id' => 'required|exists:user_cart,cart_id',
            'Cart_Quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            session(['message'=>"Invalid Quantity ". $rq->Cart_Quantity , 'type'=>'danger']);
        }else{

            $Cart_Id = $rq->Cart_Id;
            $Cart_Quantity = $rq->Cart_Quantity;
            
            $result = UserCartModel::find($Cart_Id);
            $result->cart_quantity = $Cart_Quantity;
            $result->save();

            $getProduct = ProductsModel::find($result->product_id);
            session(['message'=>"Updated ". $getProduct->product_name . "'s quantity.", 'type'=>'success']);
        }
    }

    public function DeleteCart(Request $rq){
        
        $validator = Validator::make($rq->all(),[
            'Cart_Id' => 'required|exists:user_cart,cart_id',
        ]);

        if ($validator->fails()) {
            session(['message'=>"Cart item not found!" , 'type'=>'danger']);
        }else{

            $Cart_Id = $rq->Cart_Id;
            
            $result = UserCartModel::find($Cart_Id);
            $result->delete();

            $getProduct = ProductsModel::find($result->product_id);
            session(['message'=>"Removed " . $getProduct->product_name . " from cart.", 'type'=>'success']);
        }
    }

    public function ClearCart(Request $rq){
        
        $user_id = session('user_id');
            
        $result = UserCartModel::where('user_id', '=', $user_id);
        $result->delete();

        session(['message'=>"Removed all items from cart.", 'type'=>'success']);
        
    }

    public function SearchProduct(Request $rq){
        
        // Search product
        $search = $rq->input('Product_Search');
        $searchproducts = ProductsModel::where('product_name', 'LIKE', '%'.$search.'%')->get();
        
        $customer = CustomerModel::all();
        $user_id = session('user_id');

        $cart = UserCartModel::join('users', 'user_cart.user_id', '=', 'users.user_id')
        ->join('products', 'user_cart.product_id', '=', 'products.product_id')
        ->where('users.user_id', '=', $user_id)
        ->get();

        return view('admin.cart', ['cart'=>$cart, 'customer'=>$customer, 'products'=>$searchproducts]);
    }

}
