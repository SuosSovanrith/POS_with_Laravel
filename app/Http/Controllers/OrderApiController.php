<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\UserCartModel;
use App\Models\PaymentModel;

class OrderApiController extends Controller
{ 
    public function checkKey($apikey){
        $keys = array('123456','987654');
        if(in_array($apikey, $keys)){
            return true;
        }else{
            return false;
        } 
    }

    public function AddOrder(Request $rq){

        
        if(!$this->checkKey($rq->header('api_key'))){
            $data = [
                'status'=>403,
                'message'=>'Invalid Api Key',
            ];
            return response()->json($data);
            
        }else{

            $customer_id = $rq->header('customer_id');
            $amount = $rq->header('amount');
            $discount = $rq->header('discount');
            $payment_method = $rq->header('payment_method');
            $user_id = $rq->header('user_id');

            $validator = Validator::make(['customer_id' => $customer_id, 'amount' => $amount, 'discount' => $discount, 'payment_method' => $payment_method, 'user_id' => $user_id],[
                'customer_id' => 'nullable|integer|exists:customer,customer_id',
                'amount' => 'required|numeric|min:0',
                'discount' => 'required|numeric|min:0',
                'payment_method' => 'required|numeric',
                'user_id' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                $data = [
                    'status'=>400,
                    'message'=>'Invalid input!',
                ];
                return response()->json($data);

            }else{

                $order = new OrderModel();
                $order->customer_id = $customer_id;
                $order->user_id = $user_id;
                $order->discount = $discount;
                $order->total = 0;
                $order->save();

                $cart = UserCartModel::join('users', 'user_cart.user_id', '=', 'users.user_id')
                ->join('products', 'user_cart.product_id', '=', 'products.product_id')
                ->where('users.user_id', '=', $user_id)
                ->get();

                $total = 0;

                foreach ($cart as $item){
                    $orderitem = new OrderItemModel();
                    $orderitem->order_price = $item->price_out;
                    $orderitem->order_quantity = $item->cart_quantity;
                    $orderitem->product_id = $item->product_id;
                    $orderitem->order_id = $order->order_id;
                    $orderitem->save();
                    $total += $orderitem->order_price * $orderitem->order_quantity;
                }

                $order->total = $total * (1 - ($discount/100));
                $order->save();

                $payment = new PaymentModel();
                $payment->user_id = $user_id;
                $payment->order_id = $order->order_id;
                $payment->amount = $amount;
                $payment->payment_method = $payment_method;
                $payment->save();

                $result = UserCartModel::where('user_id', '=', $user_id);
                $result->delete();
                                    
                $data = [
                    'status'=>200,
                    'message'=>"Order has been placed.",
                ];
                return response()->json($data);
            }

        }
    }
  
    public function GetLastOrder(Request $rq){

        if(!$this->checkKey($rq->header('api_key'))){
            $data = [
                'status'=>403,
                'message'=>'Invalid Api Key',
            ];
            return response()->json($data);
            
        }else{

            $order = OrderModel::join('payment', 'orders.order_id', '=', 'payment.order_id')
            ->join('customer', 'orders.customer_id', '=', 'customer.customer_id')
            ->join('users', 'orders.user_id', '=', 'users.user_id')
            ->select(['orders.order_id', 'orders.discount', 'orders.total','orders.created_at', 'customer.customer_name', 'users.name', 'payment.amount'])
            ->orderBy('orders.created_at', 'desc')->first();
            $orderitem = OrderItemModel::join('products', 'orderitem.product_id', '=', 'products.product_id')->where('orderitem.order_id', $order->order_id)->get();
          
            $data = [
                'status'=>200,
                'message'=>"Latest order data recieved.",
                'order'=>$order,
                'orderitem'=>$orderitem,
            ];
            return response()->json($data);
            
        }
    }

}
