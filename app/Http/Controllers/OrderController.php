<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\UserCartModel;
use App\Models\PaymentModel;
use Illuminate\Support\Carbon;

class OrderController extends Controller
{
    public function OrderView(){

        $orders = OrderModel::join('customer', 'orders.customer_id', '=', 'customer.customer_id')
        ->join('users', 'orders.user_id', '=', 'users.user_id')
        ->join('payment', 'orders.order_id', '=', 'payment.order_id')
        ->select(['orders.order_id', 'orders.discount', 'orders.total','orders.created_at', 'customer.customer_name', 'users.name', 'payment.amount', 'payment.khqr'])
        ->latest()->paginate(10);

        return view('admin.order', ['orders'=>$orders]);
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

    public function SearchView(Request $rq){

        if($rq->Filter_Time == "" && $rq->Filter_Period == "" && $rq->Start_Date == "" && $rq->End_Date == ""){
            return redirect('/admin/order');
        }

        $orders = OrderModel::join('customer', 'orders.customer_id', '=', 'customer.customer_id')
        ->join('users', 'orders.user_id', '=', 'users.user_id')
        ->join('payment', 'orders.order_id', '=', 'payment.order_id')
        ->select(['orders.order_id', 'orders.discount', 'orders.total','orders.created_at', 'customer.customer_name', 'users.name', 'payment.amount', 'payment.khqr']);
        
        // Filter time
        if($rq->Filter_Time != ""){

            if($rq->Filter_Time == "Morning"){
                $Start_Time = "06:00:00";
                $End_Time = "11:59:59";
            }elseif($rq->Filter_Time == "Afternoon"){
                $Start_Time = "12:00:00";
                $End_Time = "16:59:59";
            }elseif($rq->Filter_Time == "Evening"){
                $Start_Time = "17:00:00";
                $End_Time = "20:59:59";
            }elseif($rq->Filter_Time == "Night"){
                $Start_Time = "21:00:00";
                $End_Time = "23:59:59";
            }
        
            $orders = $orders->whereTime('orders.created_at', '>=', $Start_Time)
                ->whereTime('orders.created_at', '<=', $End_Time);
        }

        // Filter period
        if($rq->Filter_Period == "Today"){
            $orders = $orders->whereDate('orders.created_at', '>=', Carbon::today());
        }elseif($rq->Filter_Period == "Yesterday"){
            $orders = $orders->whereDate('orders.created_at', '>=', Carbon::yesterday());
        }

        if($rq->Filter_Period == "This Week"){
            $orders = $orders->whereBetween('orders.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);       
        }elseif($rq->Filter_Period == "Last Week"){
            $orders = $orders->whereBetween('orders.created_at', [Carbon::now()->subWeek(), Carbon::now()]);
        }

        if($rq->Filter_Period == "This Month"){
            $orders = $orders->whereMonth('orders.created_at', Carbon::now()->month);       
        }elseif($rq->Filter_Period == "Last Month"){
            $orders = $orders->whereMonth('orders.created_at', Carbon::now()->subMonth()->month);   
        }
        
        if($rq->Filter_Period == "This Year"){
            $orders = $orders->whereYear('orders.created_at', Carbon::now()->year);  
        }elseif($rq->Filter_Period == "Last Year"){
            $orders = $orders->whereMonth('orders.created_at', Carbon::now()->subYear()->year);   
        }

        // if filter by period, then DON'T filter from to date
        if($rq->Filter_Period == ""){
            // Filter from to date
            if($rq->Start_Date != ""){
                $Start_Date = $rq->Start_Date;
                $orders = $orders->where('orders.created_at', '>=', $Start_Date);
            }
            if($rq->End_Date != ""){
                $End_Date = $rq->End_Date;
                $orders = $orders->where('orders.created_at', '<=', $End_Date);
            }
        }
        
        $Start_Date = "";
        $End_Date = "";
    
        $orders = $orders->latest()->paginate(10);

        return view('admin.order', ['orders'=>$orders, 'filter_time'=>$rq->Filter_Time, 'filter_period'=>$rq->Filter_Period, 'start_date'=>$Start_Date, 'end_date'=>$End_Date]);
    }

    public function GetOrderItem($order_id){

        return response()->json(OrderItemModel::join('products', 'orderitem.product_id', '=', 'products.product_id')->where('orderitem.order_id', $order_id)->get(), 200);
    }
}
