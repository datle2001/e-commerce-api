<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository
{
  public function getProducts(string $orderId): array
  {
    return Order::find($orderId)->products()->withPivot(['quantity', 'can_fulfill'])->get()->toArray();
  }

  public function createOrder($userId): string
  {
    $order = new Order([
      'user_id' => $userId
    ]);

    $order->save();

    return $order->id;
  }

  public function saveProduct(array $orderedProduct): void
  {
    Order::find($orderedProduct['order_id'])
      ->products()
      ->attach($orderedProduct['product_id'], $orderedProduct);
  }
}