<?php

namespace App\DTOs;
class UserDTO {
  public string $firstName;
  public string $lastName;
  public int $sex;
  public string $dob;
  public string $email;
  public string $password;
  public string $stripeId;

  public function __construct(string $firstName, string $lastName, string $sex, string $dob, string $email, string $password) {
    $this->firstName = $firstName;
    $this->lastName = $lastName;
    $this->sex = $sex === 'M' ? 1 : 0;
    $this->dob = $dob;
    $this->email = $email;
    $this->password = $password; 
  }
}
