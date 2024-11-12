<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use App\DTOs\UserDTO;
use Stripe\Product;
use Stripe\StripeClient;

class StripeService
{

  public function __construct(private OrderRepository $orderRepository, private ProductRepository $productRepository)
  {
  }

  public function createStripeUser(UserDTO $userDTO): string
  {
    $stripe = new StripeClient(config('api.stripe_secret'));

    try {
      $customer = $stripe->customers->create([
        'email' => $userDTO->email,
        'name' => $userDTO->firstName . ' ' . $userDTO->lastName,
      ]);

      return $customer['id'];
    } catch (ApiErrorException $e) {
      Log::error($e->getMessage());

      return "";
    }
  }

  public function getStripeCheckoutLinkFrom(string $orderId, string $origin, string $referer): array
  {
    $stripe = new StripeClient(config('api.stripe_secret'));

    $originURL = str_contains($origin, 'github') ? $origin . '/e-commerce-client/' : $referer;

    $lineItems = $this->createLineItemsFrom($orderId, $stripe);

    $paymentLink = $stripe->paymentLinks->create([
      'line_items' => $lineItems,
      'phone_number_collection' => [
        'enabled' => false
      ],
      'submit_type' => 'pay',
      'after_completion' => [
        'type' => 'redirect',
        'redirect' => [
          'url' => $originURL . "/confirmation/" . $orderId
        ]
      ],
    ]);

    return ['data' => $paymentLink, 'statusCode' => 201];
  }

  /**
   * @return array line items for payment link
   */
  public function createLineItemsFrom(string $orderId, StripeClient $stripe): array
  {
    $orderedProducts = $this->orderRepository->getProducts($orderId);
    
    $lineItems = [];

    foreach ($orderedProducts as $orderedProduct) {
      $productDB = $this->productRepository->getProduct($orderedProduct['id']);
      $productStripe = null;

      if ($productDB['stripe_id'] ?? null === null) {
        $productStripe = $this->createStripeProduct($productDB['name']);

        $this->productRepository->updateProduct(
          $productDB['id'],
          ['stripe_id' => $productStripe->id]
        );

      } else {
        $productStripe = $stripe->products->retrieve($productDB['stripe_id']);
      }

      $priceStripe = $stripe->prices->create([
        'unit_amount' => $productDB['price'] * 100,
        'currency' => 'usd',
        'product' => $productStripe->id,
      ]);

      $lineItems[] = [
        'price' => $priceStripe->id,
        'quantity' => $orderedProduct['pivot']['quantity'],
      ];
    }

    return $lineItems;
  }

  /**
   * Create a Stripe version of the $productName
   * 
   * @param mixed $productName
   * @return \Stripe\Product
   */
  public function createStripeProduct($productName): Product {
    $stripe = new StripeClient(config('api.stripe_secret'));
    
    return $stripe->products->create([
      'name' => $productName,
    ]);
  }
}
