<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use App\DTOs\UserDTO;

class StripeService {
  public function createStripeUser(UserDTO $userDTO): string
  {
    $stripe = new \Stripe\StripeClient(config('api.stripe_secret'));

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
}