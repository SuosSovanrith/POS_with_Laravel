<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{

    public function DashboardView(){

        $order = OrderModel::count();

        $costProductSoldRecord = DB::table('orderitem')->join('products', 'orderitem.product_id', '=', 'products.product_id')->get();
        $totalProductSoldRecord = DB::table('orders')->join('payment', 'orders.order_id', '=', 'payment.order_id')->get();
        $totalProductCostRecord = DB::table('products')->get();

        $costProductSold = 0;
        $totalProductSold = 0;
        $totalProductCost = 0;

        foreach ($costProductSoldRecord as $record){
            $costProductSold += $record->order_quantity * $record->price_in;
        }

        foreach ($totalProductSoldRecord as $record){
            $totalProductSold += $record->amount;
        }

        foreach ($totalProductCostRecord as $record){
            $totalProductCost += $record->quantity * $record->price_in;
        }

        $totalProductCost += $costProductSold;

        $income = $totalProductSold - $costProductSold;

        // Top selling product
        $topproduct = OrderItemModel::join('products', 'orderitem.product_id', '=', 'products.product_id')
                    ->join('payment', 'orderitem.order_id', '=', 'payment.order_id')
                    ->select('products.product_name', 
                                'products.image', 
                                DB::raw('SUM(orderitem.order_quantity) as total_sold'), 
                                DB::raw('SUM(payment.amount) as total_sale'),
                                DB::raw('( SUM(payment.amount) - SUM(orderitem.order_quantity) * products.price_in) as total_profit'))
                    ->groupBy('products.product_name')
                    ->groupBy('products.image')
                    ->groupBy('products.price_in')
                    ->orderByDesc('total_sold')
                    ->limit(5)
                    ->get();

        return view('admin.index', ['order'=>$order, 'income'=>$income, 'sale'=>$totalProductSold, 'expense'=>$totalProductCost, 'topproduct'=>$topproduct]);
    }

    public function YearlyIncome(){

        $monthly_income = [];
        $monthly_sale = [];

        for($month = 1; $month<13; $month++){

            $costProductSoldRecord = DB::table('orderitem')
                                    ->join('products', 'orderitem.product_id', '=', 'products.product_id')
                                    ->whereMonth('orderitem.created_at', '=', $month)
                                    ->get();


            $totalProductSoldRecord = DB::table('orders')
                                    ->join('payment', 'orders.order_id', '=', 'payment.order_id')
                                    ->whereMonth('orders.created_at', '=', $month)
                                    ->get();

            $costProductSold = 0;
            $totalProductSold = 0;
            
                foreach ($costProductSoldRecord as $record){
                    $costProductSold += $record->order_quantity * $record->price_in;
                }
        
                foreach ($totalProductSoldRecord as $record){
                    $totalProductSold += $record->amount;
                }
                
                $income = $totalProductSold - $costProductSold;
                
            $monthly_sale[] = $totalProductSold;
            $monthly_income[] = $income;
        }
        
        $yearly_income = 0;

        foreach ($monthly_income as $value){
            $yearly_income += $value;
        }

        $data = [
            'yearly_income' => $yearly_income,
            'monthly_income' => $monthly_income,
            'monthly_sale' => $monthly_sale,
            'status' => 200
        ];

        return response()->json($data);
    }

}
