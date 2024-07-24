<?php

namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService {
  private $productRepository;

  public function __construct(ProductRepository $productRepository) {
    $this->productRepository = $productRepository;
  }

  public function getProducts(int $pageSize, int $pageIndex) {
    $products = $this->productRepository->getProducts(
      $pageSize, 
      $pageIndex, 
      ['description', 'stripe_id']
    );

    foreach ($products as $key => $product) {
      $this->addPhotoUrl($product);
    }

    // $key = $pageSize.' '.$pageIndex;

    // $products = Cache::remember($key, 60, function () use ($pageSize, $pageIndex) {
    //     return Product::paginate($pageSize, ['*'], 'page', $pageIndex)->all();
    // });

    return $products;
  }

  public function getProduct(string $id) {
    $product = $this->productRepository->getProduct($id, ['stripe_id']);
    $this->addPhotoUrl($product);

    return $product;
  }

  public function updateProduct(string $id, array $updates): bool {
    return $this->productRepository->updateProduct($id, $updates);
  }

  private function addPhotoUrl($product) {
    $product['photoUrl'] = env("GOOGLE_STORAGE_URL") . '/products/' . $product['photo_key'];
    unset($product['photo_key']);
  }
}