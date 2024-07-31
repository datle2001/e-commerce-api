<?php

namespace App\Services;

use App\DTOs\UserDTO;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\StripeService;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class UserService
{
  public function __construct(private UserRepository $userRepository, private StripeService $stripeService, private SharedService $sharedService) {}

  private function createUser(UserDTO $userDTO): string
  {
    $user = $this->userRepository->createUser($userDTO);

    $stripeId = $this->stripeService->createStripeUser($userDTO);
    $this->userRepository->saveStripeId($userDTO->email, $stripeId);

    return $user->createToken($user->id)->plainTextToken;
  }

  public function loginUser($validatedFields): array
  {
    $user = $this->userRepository->findUser('email', $validatedFields['email']);

    if (!$user || !Hash::check($validatedFields['password'], $user->password)) {
      return ['message' => 'Invalid credentials', 'statusCode' => 401];
    }

    $token = $user->createToken($user->id)->plainTextToken;

    return ['data' => $this->createToken($token), 'statusCode' => 201];
  }

  public function loginWithToken($token) {
    return ['data' => $this->createToken($token), 'statusCode' => 201];
  } 
  public function signupUser($validatedFields): array
  {
    $response = $this->sharedService->verifyEmail($validatedFields['email']);

    if ($response['error']) {
      return [
        'message' => 'We cannot verify your email right now. Please retry signing up later or call our support team.',
        'statusCode' => 401
      ];
    }

    if ($response['response']->status == 'valid') {
      $userDTO = new UserDTO(
        $validatedFields['firstName'],
        $validatedFields['lastName'],
        $validatedFields['sex'],
        $validatedFields['dob'],
        $validatedFields['email'],
        Hash::make($validatedFields['password'])
      );

      $token = $this->createUser($userDTO);

      return [
        'data' => $this->createToken($token),
        'statusCode' => 201
      ];
    }

    if ($response['response']->status == 'invalid') {
      return [
        'message' =>
          'We cannot verify your email. Please check your email address.',
        'statusCode' => 510
      ];
    }

    return ['message' => 'Contact Support Group', 'statusCode' => 500];
  }

  private function createToken($token)
  {
    return [
      'token' => 'Bearer ' . $token,
      'expireBy' => now()->addDay()->toDateTimeString()
    ];
  }
}