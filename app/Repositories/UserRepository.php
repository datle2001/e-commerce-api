<?php

namespace App\Repositories;
use App\DTOs\UserDTO;
use App\Models\User;

class UserRepository {
  public function createUser(UserDTO $userDTO): User
  {
    return User::create([
      "first_name" => $userDTO->firstName,
      "last_name" => $userDTO->lastName,
      "password" => $userDTO->password,
      "email" => $userDTO->email,
      "sex" => $userDTO->sex,
      "dob" => $userDTO->dob
    ]);
  }

  public function saveStripeId(string $email, string $stripeId) {
    $user = User::where('email', $email)->first();
    $user['stripe_id'] = $stripeId;
    $user->save();
  }

  public function findUser(string $column, $value): User | null 
  {
    return User::where($column, $value)->first();
  }
}