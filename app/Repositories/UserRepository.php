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
}