<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\Schema;

class ProductRepository
{
  public function getProducts(int | null $pageSize, int | null $pageIndex, array | null $stringIds, array $excludedColumns = []): array
  {
    $query = Product::query();

    if($stringIds != null) {
      $query = $query->whereIn('id', $stringIds);
    }

    return $query->paginate(
      $pageSize,
      $this->filterColumns($excludedColumns),
      'page',
      $pageIndex
    )->all();
  }

  public function getProduct(string $id, array $excludedColumns = []): array
  {
    return Product::find($id)
      ->select($this->filterColumns($excludedColumns))
      ->first()
      ->toArray();
  }

  public function updateProduct(string $id, array $updates): bool
  {
    return Product::find($id)->update($updates);
  }

  private function filterColumns(array $excludedColumns): array
  {
    return array_diff(Schema::getColumnListing('products'), $excludedColumns);
  }
}