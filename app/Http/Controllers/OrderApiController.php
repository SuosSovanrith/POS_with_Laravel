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

        $validator = Validator::make($rq->all(),[
            'customer_id' => 'nullable|integer|exists:customer,customer_id',
            'amount' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            session(['message'=>"Invalid input!" , 'type'=>'danger']);
        }else{

            $user_id = session('user_id');

            $order = new OrderModel();
            $order->customer_id = $rq->customer_id;
            $order->user_id = $user_id;
            $order->discount = $rq->discount;
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

            $order->total = $total * (1 - ($rq->discount/100));
            $order->save();

            $payment = new PaymentModel();
            $payment->user_id = $user_id;
            $payment->order_id = $order->order_id;
            $payment->amount = $rq->amount;
            $payment->payment_method = $rq->payment_method;
            
            
            if(isset($rq->khqr)){
                $payment->khqr = 'assets/images/payment/'.$rq->khqr;
            }

            $payment->save();

            $result = UserCartModel::where('user_id', '=', $user_id);
            $result->delete();

            session(['message'=>"Order has been placed." , 'type'=>'success']);
        }

    }
}
