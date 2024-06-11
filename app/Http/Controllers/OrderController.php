<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\UserCartModel;
use App\Models\PaymentModel;

class OrderController extends Controller
{
    public function OrderView(){

        $orders = OrderModel::join('customer', 'orders.customer_id', '=', 'customer.customer_id')
        ->join('users', 'orders.user_id', '=', 'users.user_id')
        ->join('payment', 'orders.order_id', '=', 'payment.order_id')
        ->select(['orders.order_id', 'orders.total','orders.created_at', 'customer.customer_name', 'users.name', 'payment.amount'])
        ->latest()->paginate(10);

        return view('admin.order', ['orders'=>$orders]);
    }

    public function getTotal($order_id){
        $orderitem = OrderItemModel::where('order_id', $order_id)->get();
        $total = 0;

        foreach($orderitem as $item){
            $total += $item->order_price;
        }

        return $total;
    }

    public function AddOrder(Request $rq){

        $validator = Validator::make($rq->all(),[
            'Customer_ID' => 'nullable|integer|exists:customer,customer_id',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            session(['message'=>"Invalid Customer" , 'type'=>'danger']);
        }else{

            $user_id = session('user_id');

            $order = new OrderModel();
            $order->customer_id = $rq->customer_id;
            $order->user_id = $user_id;
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

            $order->total = $total;
            $order->save();

            $payment = new PaymentModel();
            $payment->user_id = $user_id;
            $payment->order_id = $order->order_id;
            $payment->amount = $rq->amount;
            $payment->save();

            $result = UserCartModel::where('user_id', '=', $user_id);
            $result->delete();

            session(['message'=>"Order has been placed." , 'type'=>'success']);
        }

    }

    public function SearchView(Request $rq){

        $orders = OrderModel::join('customer', 'orders.customer_id', '=', 'customer.customer_id')
        ->join('users', 'orders.user_id', '=', 'users.user_id')
        ->join('payment', 'orders.order_id', '=', 'payment.order_id')
        ->select(['orders.order_id', 'orders.total','orders.created_at', 'customer.customer_name', 'users.name', 'payment.amount'])
        ->where('orders.created_at', '>=', $rq->Start_Date)
        ->where('orders.created_at', '<=', $rq->End_Date)
        ->latest()->paginate(10);

        return view('admin.order', ['orders'=>$orders, 'start_date'=>$rq->Start_Date, 'end_date'=>$rq->End_Date]);
    }
}
