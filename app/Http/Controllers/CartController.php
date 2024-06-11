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

        $customer = CustomerModel::select(['customer_id', 'customer_name'])->get();
        $products = ProductsModel::select(['product_id', 'product_name', 'quantity', 'image', 'in_stock'])->orderBy('in_stock', 'desc')->paginate(12);

        $user_id = session('user_id');

        $cart = UserCartModel::join('users', 'user_cart.user_id', '=', 'users.user_id')
        ->join('products', 'user_cart.product_id', '=', 'products.product_id')
        ->select('user_cart.cart_id', 'user_cart.product_id', 'products.product_name', 'products.image', 'user_cart.cart_quantity', 'user_cart.created_at', 'products.price_out')
        ->latest()
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

            $product = ProductsModel::where('barcode', '=', $barcode)->select(['product_id', 'product_name', 'quantity'])->first();
            
            // Check if already have product in cart
            $checkInCart = UserCartModel::join('products', 'user_cart.product_id', '=', 'products.product_id')
            ->where('products.barcode', '=', $barcode)
            ->where('user_cart.user_id', '=', $user_id)
            ->first();
            
            if(isset($checkInCart)){
                $checkInCart->cart_quantity = $checkInCart->cart_quantity + 1;
                $checkInCart->save();
                
                session(['message'=>"Added 1 ". $checkInCart->product_name . " to cart.", 'type'=>'danger']);
                }else{
                    
                
                if($product->quantity < 1){
                    session(['message'=>$product->product_name . " out of stock.", 'type'=>'danger']);

                }else{
                    $result = new UserCartModel();
        
                    $result->user_id = $user_id;
                    $result->product_id = $product->product_id;
                    $result->cart_quantity = 1;
                    $result->save();
                    
                    session(['message'=>"Added ". $product->product_name . " to cart.", 'type'=>'success']);
                }

            }

            // Update product quantity 
            $product->quantity = $product->quantity - 1;
            $product->save();

        }
    }

    public function AddCartImage($product_id){
            
        $user_id = session('user_id');
        
        $product = ProductsModel::where('product_id', '=', $product_id)->select(['product_id', 'product_name', 'quantity'])->first();

        // Check if already have product in cart
        $checkInCart = UserCartModel::join('products', 'user_cart.product_id', '=', 'products.product_id')
        ->where('products.product_id', '=', $product_id)
        ->where('user_cart.user_id', '=', $user_id)
        ->first();

        if(isset($checkInCart)){
            $checkInCart->cart_quantity = $checkInCart->cart_quantity + 1;
            $checkInCart->save();
            
            session(['message'=>"Added 1 ". $checkInCart->product_name . " to cart.", 'type'=>'success']);

        }else{
            
            $result = new UserCartModel();
        
            $result->user_id = $user_id;
            $result->product_id = $product_id;
            $result->cart_quantity = 1;
            $result->save();

            session(['message'=>"Added ". $product->product_name . " to cart.", 'type'=>'success']);
            
        }
            
        // Update product quantity 
        $product->quantity = $product->quantity - 1;
        $product->save();
            
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

            // Get true update quantity
            $update_quantity = $Cart_Quantity - $result->cart_quantity;

            // Check if there are enough in stock
            $product = ProductsModel::find($result->product_id);

            if($product->quantity < $update_quantity){
                session(['message'=>"Not enough ". $product->product_name . " in stock (" . $product->quantity . ")", 'type'=>'danger']);

            }else{

                $result->cart_quantity = $Cart_Quantity;
                $result->save();
    
                // Update product quantity 
                $product->quantity = $product->quantity - $update_quantity;
                $product->save();
    
                session(['message'=>"Updated ". $product->product_name . "'s quantity.", 'type'=>'success']);

            }
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
            $cart_quantity = $result->cart_quantity;

            $result->delete();

            // Update product quantity 
            $product = ProductsModel::find($result->product_id);
            $product->quantity = $product->quantity + $cart_quantity;
            $product->save();

            session(['message'=>"Removed " . $product->product_name . " from cart.", 'type'=>'success']);
        }
    }

    public function ClearCart(Request $rq){
        
        $user_id = session('user_id');
            
        $cart = UserCartModel::where('user_id', '=', $user_id)->select(['product_id', 'cart_quantity'])->get();

        // update product quantity
        foreach ($cart as $item){
            $product = ProductsModel::find($item->product_id);
            $product->quantity = $product->quantity + $item->cart_quantity;
            $product->save();   
        }

        $result = UserCartModel::where('user_id', '=', $user_id);
        $result->delete();

        session(['message'=>"Removed all items from cart.", 'type'=>'success']);
        
    }

    public function SearchProduct(Request $rq){
        
        // Search product
        $search = $rq->input('Product_Search');
        $searchproducts = ProductsModel::where('product_name', 'LIKE', '%'.$search.'%')->select(['product_id', 'product_name', 'quantity', 'image', 'in_stock'])->orderBy('in_stock', 'desc')->paginate(12);
        
        $customer = CustomerModel::select(['customer_id', 'customer_name'])->get();
        $user_id = session('user_id');

        $cart = UserCartModel::join('users', 'user_cart.user_id', '=', 'users.user_id')
        ->join('products', 'user_cart.product_id', '=', 'products.product_id')
        ->where('users.user_id', '=', $user_id)
        ->select('user_cart.cart_id', 'user_cart.product_id', 'products.product_name', 'products.image', 'user_cart.cart_quantity', 'user_cart.created_at', 'products.price_out')
        ->latest()
        ->get();

        return view('admin.cart', ['cart'=>$cart, 'customer'=>$customer, 'products'=>$searchproducts]);
    }

}
