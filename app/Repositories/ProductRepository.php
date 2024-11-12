<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\Schema;

class ProductRepository
{
  public function getProducts(int|null $pageSize, int|null $pageIndex, array|null $stringIds, array $excludedColumns = []): array
  {
    $query = Product::query();

    if ($stringIds != null) {
      $query = $query->whereIn('id', $stringIds);
    }

    $products = $query->paginate(
      $pageSize,
      $this->filterColumns($excludedColumns),
      'page',
      $pageIndex
    )->all();

    foreach ($products as $key => $product) {
      $this->addPhotoUrl($product);
    }

    return $products;
  }

  public function getProduct(string $id, array $excludedColumns = [])
  {
    $product = Product::find(
      $id,
      $this->filterColumns($excludedColumns)
    );

    $this->addPhotoUrl($product);
    return $product;
  }

  public function updateProduct(string $id, array $updates): bool
  {
    return Product::find($id)->update($updates);
  }

  private function filterColumns(array $excludedColumns): array
  {
    return array_diff(Schema::getColumnListing('products'), $excludedColumns);
  }

  private function addPhotoUrl($product): void
  {
    $product['photoUrl'] = config('api.google_storage_url') . '/products/' . $product['photo_key'];
    unset($product['photo_key']);
  }
}