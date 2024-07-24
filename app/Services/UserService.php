<?php

namespace App\Services;

use App\DTOs\UserDTO;
use App\Repositories\UserRepository;
use App\Services\StripeService;

class UserService {
  private $userRepository;
  private $stripeService;

  public function __construct(UserRepository $userRepository, StripeService $stripeService) {
    $this->userRepository = $userRepository;
    $this->stripeService = $stripeService;
  }
  public function createUser(UserDTO $userDTO): string
  {
    $user = $this->userRepository->createUser($userDTO);
    $user['stripe_id'] = $this->stripeService->createStripeUser($userDTO);

    return $user->createToken($user->id)->plainTextToken;
  }

  public function createToken($token)
  {
    return [
      'token' => 'Bearer ' . $token,
      'expireBy' => now()->addDay()->toDateTimeString()
    ];
  }
}