<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;

class OrderService
{
  public function __construct(private OrderRepository $orderRepository, private ProductRepository $productRepository, private ProductService $productService)
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
        'can_fulfill' => $productDB['quantity'] > $selectedProduct['quantity']
      ];

      $this->orderRepository->saveProduct($orderedProduct);

      if ($orderedProduct['can_fulfill']) {
        $this->productRepository->updateProduct(
          $productDB['id'],
          [
            'quantity' =>
              $productDB['quantity'] - $selectedProduct['quantity']
          ]
        );
      }
    }

    return $orderId;
  }

  public function getOrder(string $id): array
  {
    // fix add photo url
    $rawOrderedProducts = $this->orderRepository->getProducts($id);

    $orderedProducts = [];

    foreach ($rawOrderedProducts as $index => $rawOrderedProduct) {
      $product = $this->productRepository->getProduct(
          $rawOrderedProduct['pivot']['product_id'],
          ["description", 'stripe_id']
      );

      $orderedProduct = [
        'product' => $product,
        'can_fulfill' => $rawOrderedProduct['pivot']['can_fulfill'],
        'quantity' => $rawOrderedProduct['pivot']['quantity'],
      ];
      
      $orderedProducts[] = $orderedProduct;
    }

    return ['data' => $orderedProducts, 'statusCode' => 201];
  }
}