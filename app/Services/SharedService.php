<?php

namespace App\Services;
use Illuminate\Http\JsonResponse;

class SharedService {
  public function verifyEmail(string $email): array {
    $curl = curl_init();
    $url = config('api.rapid_api_url') . http_build_query(['email' => $email]);

    curl_setopt_array($curl, [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => [
        config('api.rapid_api_host'), 
        config('api.rapid_api_key')
      ],
    ]);

    $emailValidationResponse = json_decode(curl_exec($curl));
    $emailValidationErr = curl_error($curl);

    curl_close($curl);

    return [
      'response' => $emailValidationResponse,
      'error' => $emailValidationErr
    ];
  }

  public function handleServiceResponse(array $response): JsonResponse {
    if (array_key_exists('message', $response)) {
      return response()->json($response['message'], $response['statusCode']);
    }

    return response()->json($response['data'], $response['statusCode']);
  }
}