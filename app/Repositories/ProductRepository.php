<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\Schema;

class ProductRepository {

  public function getProducts(int $pageSize, int $pageIndex, array $excludedColumns = []): array {
    return Product::paginate($pageSize, 
      $this->filterColumns($excludedColumns), 
      'page', 
      $pageIndex
    )->all();
  }

  public function getProduct(string $id, array $excludedColumns = []) {
    return Product::find($id, $this->filterColumns($excludedColumns));
  }

  public function updateProduct(string $id, array $updates): bool {
    $product =  Product::find($id);
    $newUpdates = [];

    if ($updates['rating']) {
      $newUpdates['num_rating'] = $product->num_rating + 1;
      $newUpdates['rating'] = ($product->rating * $product->num_rating + $updates['rating']) / ($product->num_rating + 1);
    }

    return $product->update($newUpdates);
  }

  private function filterColumns(array $excludedColumns): array {
    return array_diff(Schema::getColumnListing('products'), $excludedColumns);
  }
}