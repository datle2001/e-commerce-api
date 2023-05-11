<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\ProductOrder;
class OrderController extends Controller
{
    /**
     * Get all orders of the authenticated user
     * 
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $user = auth('sanctum')->user();
        $orders = $user->orders;
        foreach($orders as $order) {
            unset($order->user_id);
        }
    
        return response($orders, 201);
    }

    /**
     * Get product list of an order
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function getProductIDQuantityList($id) {
        $productID_quantity_list = ProductOrder::where('order_id', $id)->get(['product_id', 'quantity']);

        foreach($productID_quantity_list as $productID_quantity) {
            $productID_quantity['id'] = $productID_quantity['product_id'];
            $productID_quantity['quantity_purchase'] = $productID_quantity['quantity'];
            unset($productID_quantity->product_id);
            unset($productID_quantity->quantity);
        }
        return response($productID_quantity_list, 201);
    }
}
