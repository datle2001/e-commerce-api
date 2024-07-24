<?php

namespace App\DTOs;
class UserDTO {
  public string $firstName;
  public string $lastName;
  public string $sex;
  public string $dob;
  public string $email;
  public string $password;
  public string $stripeId;

  public function __construct(string $firstName, string $lastName, string $sex, string $dob, string $email, string $password) {
    $this->firstname = $firstName;
    $this->lastName = $lastName;
    $this->sex = $sex;
    $this->dob = $dob;
    $this->email = $email;
    $this->password = $password; 
  }
}
