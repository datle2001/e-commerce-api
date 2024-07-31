<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;

class OrderService
{
  public function __construct(private OrderRepository $orderRepository, private ProductRepository $productRepository)
  {
  }

  public function createOrder(string $userId, array $selectedProducts): string
  {
    $orderId = $this->orderRepository->createOrder($userId);

    foreach ($selectedProducts as $selectedProduct) {
      $product = $selectedProduct['product'];
      $productDB = $this->productRepository->getProduct($product['id']);

      $orderedProduct = [
        'product_id' => $product['id'],
        'quantity' => $selectedProduct['quantity'],
        'order_id' => $orderId,
        'can_fulfill' => $productDB['quantity_available'] > $selectedProduct['quantity']
      ];

      $this->orderRepository->saveProduct($orderedProduct);

      if ($orderedProduct['can_fulfill']) {
        $this->productRepository->updateProduct(
          $productDB['id'],
          [
            'quantity_available' =>
              $productDB['quantity_available'] - $selectedProduct['quantity']
          ]
        );
      }
    }

    return $orderId;
  }

  public function getOrder(string $id): array
  {
    $rawOrderedProducts = $this->orderRepository->getProducts($id);

    $orderedProducts = [];

    foreach ($rawOrderedProducts as $index => $rawOrderedProduct) {
      $orderedProduct = [
        'product' => $this->productRepository->getProduct(
          $rawOrderedProduct['pivot']['product_id'],
          ["description", 'stripe_id']
        ),
        'can_fulfill' => $rawOrderedProduct['can_fulfill'],
        'ordered_quantity' => $rawOrderedProduct['quantity'],
      ];

      $orderedProducts[] = $orderedProduct;
    }

    return ['data' => $orderedProducts, 'statusCode' => 201];
  }
}