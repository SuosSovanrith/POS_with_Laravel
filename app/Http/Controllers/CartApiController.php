<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserCartModel;
use App\Models\CustomerModel;
use App\Models\ProductsModel;
use Illuminate\Support\Facades\Validator;

class CartApiController extends Controller
{

    public function checkKey($apikey){
        $keys = array('123456','987654');
        if(in_array($apikey, $keys)){
            return true;
        }else{
            return false;
        } 
    }

    public function CartView(Request $rq){

        if(!$this->checkKey($rq->header('api_key'))){
            $data = [
                'status'=>403,
                'message'=>'Invalid Api Key',
            ];
            return response()->json($data);
            
        }else{

            $user_id = $rq->header('user_id');

            $customer = CustomerModel::select(['customer_id', 'customer_name'])->get();
            $products = ProductsModel::select(['product_id', 'product_name', 'quantity', 'image', 'in_stock'])->orderBy('quantity', 'desc')->get();

            $cart = UserCartModel::join('users', 'user_cart.user_id', '=', 'users.user_id')
            ->join('products', 'user_cart.product_id', '=', 'products.product_id')
            ->select('user_cart.cart_id', 'user_cart.product_id', 'products.product_name', 'products.image', 'user_cart.cart_quantity', 'user_cart.created_at', 'products.price_out')
            ->latest()
            ->where('users.user_id', '=', $user_id)
            ->get();
    
            $data = ['status'=>200, 'cart'=>$cart, 'customer'=>$customer, 'products'=>$products];
            return response()->json($data);

        }

    }

    public function AddCart(Request $rq){

        if(!$this->checkKey($rq->header('api_key'))){
            $data = [
                'status'=>403,
                'message'=>'Invalid Api Key',
            ];
            return response()->json($data);
            
        }else{

            $user_id = $rq->header('user_id');;
            $barcode = $rq->header('Barcode');

            $validator = Validator::make(['barcode' => $barcode],[
                'barcode' => 'required|exists:products,barcode'
            ]);

            if ($validator->fails()) {
                $data = [
                    'status'=>400,
                    'message'=>'Invalid Barcode',
                ];
                return response()->json($data);

            }else{

                $product = ProductsModel::where('barcode', '=', $barcode)->select(['product_id', 'product_name', 'quantity'])->first();
                
                if($product->quantity < 1){
                        
                    $data = [
                        'status'=>412,
                        'message'=>$product->product_name . " out of stock.",
                    ];
                    return response()->json($data);

                }else{
                    // Check if already have product in cart
                    $checkInCart = UserCartModel::join('products', 'user_cart.product_id', '=', 'products.product_id')
                    ->where('products.barcode', '=', $barcode)
                    ->where('user_cart.user_id', '=', $user_id)
                    ->first();
                    
                    if(isset($checkInCart)){
                        $checkInCart->cart_quantity = $checkInCart->cart_quantity + 1;
                        $checkInCart->save();
                        
                        // Update product quantity 
                        $product->quantity = $product->quantity - 1;
                        $product->save();
                    
                        $data = [
                            'status'=>200,
                            'message'=>"Added 1 ". $checkInCart->product_name . " to cart.",
                        ];
                        return response()->json($data);

                    }else{
                            
                        $result = new UserCartModel();
                
                        $result->user_id = $user_id;
                        $result->product_id = $product->product_id;
                        $result->cart_quantity = 1;
                        $result->save();

                        // Update product quantity 
                        $product->quantity = $product->quantity - 1;
                        $product->save();
                            
                        $data = [
                            'status'=>200,
                            'message'=>"Added ". $product->product_name . " to cart.",
                        ];  
                        return response()->json($data);
                        
                    }
                }
            }
        }
    }

    public function AddCartImage(Request $rq){
            
        if(!$this->checkKey($rq->header('api_key'))){
            $data = [
                'status'=>403,
                'message'=>'Invalid Api Key',
            ];
            return response()->json($data);
            
        }else{

            $user_id = $rq->header('user_id');;
            $product_id = $rq->header('Product_id');

            $validator = Validator::make(['product_id' => $product_id],[
                'product_id' => 'required|exists:products,product_id'
            ]);

            if ($validator->fails()) {
                $data = [
                    'status'=>400,
                    'message'=>'Invalid Product ID',
                ];
                return response()->json($data);

            }else{
            
                $product = ProductsModel::where('product_id', '=', $product_id)->select(['product_id', 'product_name', 'quantity'])->first();

                // Check if already have product in cart
                $checkInCart = UserCartModel::join('products', 'user_cart.product_id', '=', 'products.product_id')
                ->where('products.product_id', '=', $product_id)
                ->where('user_cart.user_id', '=', $user_id)
                ->first();

                if(isset($checkInCart)){
                    $checkInCart->cart_quantity = $checkInCart->cart_quantity + 1;
                    $checkInCart->save();
                                        
                    // Update product quantity 
                    $product->quantity = $product->quantity - 1;
                    $product->save();
                                        
                    $data = [
                        'status'=>200,
                        'message'=>"Added 1 ". $checkInCart->product_name . " to cart.",
                    ];
                    return response()->json($data);

                }else{
                    
                    $result = new UserCartModel();
                
                    $result->user_id = $user_id;
                    $result->product_id = $product_id;
                    $result->cart_quantity = 1;
                    $result->save();
                    
                    // Update product quantity 
                    $product->quantity = $product->quantity - 1;
                    $product->save();
                                                
                    $data = [
                        'status'=>200,
                        'message'=>"Added ". $product->product_name . " to cart.",
                    ];  
                    return response()->json($data);
                    
                }
            }
        }
    }

    public function UpdateCartQuantity(Request $rq){

        if(!$this->checkKey($rq->header('api_key'))){
            $data = [
                'status'=>403,
                'message'=>'Invalid Api Key',
            ];
            return response()->json($data);
            
        }else{
            
            if ($rq->header('Cart_Quantity') < 1) {
                $data = [
                    'status'=>400,
                    'message'=>'Invalid Quantity'
                ];
                return response()->json($data);

            }else{

                $Cart_Id = $rq->header('Cart_Id');
                $Cart_Quantity = $rq->header('Cart_Quantity');
                
                $result = UserCartModel::find($Cart_Id);

                // Get true update quantity
                $update_quantity = $Cart_Quantity - $result->cart_quantity;

                // Check if there are enough in stock
                $product = ProductsModel::find($result->product_id);

                if($product->quantity < $update_quantity){
                    $data = [
                        'status'=>412,
                        'message'=>'Not enough '. $product->product_name . ' in stock! (' . $product->quantity . ' left)'
                    ];
                    return response()->json($data);

                }else{

                    $result->cart_quantity = $Cart_Quantity;
                    $result->save();
        
                    // Update product quantity 
                    $product->quantity = $product->quantity - $update_quantity;
                    $product->save();

                    $data = [
                        'status'=>200,
                        'message'=>'Quantity updated successfully'
                    ];
                    return response()->json($data);
                }
            }
        }
    }

    public function DeleteCart(Request $rq){
        
        if(!$this->checkKey($rq->header('api_key'))){
            $data = [
                'status'=>403,
                'message'=>'Invalid Api Key',
            ];
            return response()->json($data);
            
        }else{

            $Cart_Id = $rq->header('Cart_Id');

            $validator = Validator::make(['Cart_Id' => $Cart_Id],[
                'Cart_Id' => 'required|exists:user_cart,cart_id',
            ]);

            if ($validator->fails()) {
                $data = [
                    'status'=>400,
                    'message'=>'Cart item not found!'
                ];
                return response()->json($data);

            }else{
                
                $result = UserCartModel::find($Cart_Id);
                $cart_quantity = $result->cart_quantity;

                $result->delete();

                // Update product quantity 
                $product = ProductsModel::find($result->product_id);
                $product->quantity = $product->quantity + $cart_quantity;
                $product->save();

                $data = [
                    'status'=>200,
                    'message'=>"Removed " . $product->product_name . " from cart."
                ];
                return response()->json($data);
            }
        }
    }

    public function ClearCart(Request $rq){

        if(!$this->checkKey($rq->header('api_key'))){
            $data = [
                'status'=>403,
                'message'=>'Invalid Api Key',
            ];
            return response()->json($data);
            
        }else{
        
            $user_id = $rq->header('user_id');
                
            $cart = UserCartModel::where('user_id', '=', $user_id)->select(['product_id', 'cart_quantity'])->get();

            // update product quantity
            foreach ($cart as $item){
                $product = ProductsModel::find($item->product_id);
                $product->quantity = $product->quantity + $item->cart_quantity;
                $product->save();   
            }

            $result = UserCartModel::where('user_id', '=', $user_id);
            $result->delete();

            $data = [
                'status'=>200,
                'message'=>"Cart has been cleared."
            ];
            return response()->json($data);
        }
    }

    public function SearchProduct(Request $rq){
        
        if(!$this->checkKey($rq->header('api_key'))){
            $data = [
                'status'=>403,
                'message'=>'Invalid Api Key',
            ];
            return response()->json($data);
            
        }else{
            // Search product
            $searchproducts = ProductsModel::where('product_name', 'LIKE', '%'.$rq->header('Search').'%')->select(['product_id', 'product_name', 'quantity', 'image', 'in_stock'])->orderBy('quantity', 'desc')->get();
    
            $data = ['status'=>200, 'products'=>$searchproducts];
            return response()->json($data);
        }
    }
}
