<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderModel;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function DashboardView(){

        $order = OrderModel::count();

        $costProductSoldRecord = DB::table('orderitem')->join('products', 'orderitem.product_id', '=', 'products.product_id')->get();
        $totalProductSoldRecord = DB::table('orders')->join('payment', 'orders.order_id', '=', 'payment.order_id')->get();

        $costProductSold = 0;
        $totalProductSold = 0;

        foreach ($costProductSoldRecord as $record){
            $costProductSold += $record->order_quantity * $record->price_in;
        }

        foreach ($totalProductSoldRecord as $record){
            $totalProductSold += $record->amount;
        }

        return view('admin.index', ['order'=>$order, 'income'=>$totalProductSold - $costProductSold]);
    }
}
