<?php

namespace App\Services;

class SharedService {
  public function verifyEmail(string $email): array {
    $curl = curl_init();
    $url = env("RAPIDAPI_URL") . http_build_query(['email' => $email]);

    curl_setopt_array($curl, [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => [env("RAPIDAPI_HOST"), env("RAPIDAPI_KEY")],
    ]);

    $emailValidationResponse = json_decode(curl_exec($curl));
    $emailValidationErr = curl_error($curl);

    curl_close($curl);

    return [
      'response' => $emailValidationResponse,
      'error' => $emailValidationErr
    ];
  }
}