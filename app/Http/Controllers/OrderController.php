<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\UserCartModel;

class OrderController extends Controller
{
    public function OrderView(){

        $orders = OrderModel::latest()->paginate(10);

        return view('admin.order', ['orders'=>$orders]);
    }

    public function AddOrder(Request $rq){

        $validator = Validator::make($rq->all(),[
            'Customer_ID' => 'nullable|integer|exists:customer,customer_id',
        ]);

        if ($validator->fails()) {
            session(['message'=>"Invalid Customer" , 'type'=>'danger']);
        }else{

            $user_id = session('user_id');

            $order = new OrderModel();
            $order->customer_id = $rq->customer_id;
            $order->user_id = $user_id;
            $order->save();

            $cart = UserCartModel::join('users', 'user_cart.user_id', '=', 'users.user_id')
            ->join('products', 'user_cart.product_id', '=', 'products.product_id')
            ->where('users.user_id', '=', $user_id)
            ->get();

            foreach ($cart as $item){
                $orderitem = new OrderItemModel();
                $orderitem->order_price = $item->price_out;
                $orderitem->order_quantity = $item->cart_quantity;
                $orderitem->product_id = $item->product_id;
                $orderitem->order_id = $order->order_id;
                $orderitem->save();
            }

            $result = UserCartModel::where('user_id', '=', $user_id);
            $result->delete();

            session(['message'=>"Order has been placed." , 'type'=>'success']);
        }

    }
}
