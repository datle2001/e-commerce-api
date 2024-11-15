<?php

namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService
{

  public function __construct(private ProductRepository $productRepository)
  {
  }

  public function getProducts(int | null $pageSize = 15, int|null $pageIndex = 1, string|null $productIds): array
  {
    if ($productIds != null) {
      $productIds = array_map('intval', explode(',', $productIds));
    }

    $products = $this->productRepository->getProducts(
      $pageSize,
      $pageIndex,
      $productIds,
      ['description', 'stripe_id']
    );

    // $key = $pageSize.' '.$pageIndex;

    // $products = Cache::remember($key, 60, function () use ($pageSize, $pageIndex) {
    //     return Product::paginate($pageSize, ['*'], 'page', $pageIndex)->all();
    // });

    return ['data' => $products, 'statusCode' => 201];
  }

  public function getProduct(string $id)
  {
    $product = $this->productRepository->getProduct($id, ['stripe_id']);

    return ['data' => $product, 'statusCode' => 201];
  }

  public function updateProduct(string $id, array $updates): array
  {
    $newUpdates = [];

    if (array_key_exists('rating', $updates)) {
      $product = $this->productRepository->getProduct($id);
      $newUpdates['num_rating'] = $product['num_rating'] + 1;
      $newUpdates['rating'] = ($product['rating'] * $product['num_rating'] + $updates['rating']) / ($product['num_rating'] + 1);
    }

    if ($this->productRepository->updateProduct($id, $newUpdates)) {
      return ['data' => true, 'statusCode' => 201];
    }
    
    return ['message' => 'Please try again later.', 'statusCode' => 503];
  }
}