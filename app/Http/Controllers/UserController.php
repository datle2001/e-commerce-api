<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Validator;

class UserController extends Controller
{
    /**
     * Return all users.
     *
     * @return Collection
     */
    public function index()
    {
        return User::all();
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
            'email' => 'required|string|unique:users|email',
        ], [
            'email' => 'The :attribute must be a valid email address.',
            'unique' => 'The :attribute has already been taken.'
        ]);

        if($validator->fails()) {
            $message = $validator->getMessageBag()->first();
            return response(['message' => $message], 422);
        }

        $fields = $validator->validated();
        $curl = curl_init();
        $url = env("RAPIDAPI_URL").http_build_query(['email' => $fields['email']]);
        
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

        if ($emailValidationErr) {
            return response(['message' => 'Cannot validate email due to server error'], 401);
        }

        if($emailValidationResponse->status == 'valid') {
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $customer = $stripe->customers->create([
                'email' => $fields['email'],
                'name' => $fields['username'],
            ]);
            
            $user = User::create([
                'username' => $fields['username'],
                'password' => bcrypt($fields['password']),
                'email' => $fields['email'],
                'stripe_id' => $customer['id'],
            ]);
         
            $token = $user->createToken($user->id)->plainTextToken;
            $response = [
                'user' => $user,
                'token' => 'Bearer '.$token
            ];
            return response($response, 201);
        }

        if($emailValidationResponse->status == 'invalid') {
            return response(['message' => 'Email does not exist'], 510);
        }

        return response(['message' => 'Contact Support Group'], 500);
    }
    public function loginWithToken(Request $request) {
        $user = auth('sanctum')->user();
        $this->getCard($user);
        $response = [
            'user' => $user,
            'token' => 'Bearer '.$request->bearerToken()
        ];

        return response($response, 201);
    }

    public function login(Request $request)
    {  
        $credValidator = Validator::make($request->all(), [
            'password' => 'required|string',
            'email' => 'required|string|email',
        ], []);

        if($credValidator->fails()) {
            $message = $credValidator->getMessageBag()->first();
            return response(['message' => $message], 422);
        }

        $fields = $credValidator->validated();
        $user = User::where('email', $fields['email'])->first();
        
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken($user->id)->plainTextToken;
        $this->getCard($user);
        $response = [
            'user' => $user,
            'token' => 'Bearer '.$token
        ];
        return response($response, 201);  
    }
    
    public function logout(Request $request) {
        $user = auth('sanctum')->user();
        PersonalAccessToken::where('tokenable_id', $user->id)->delete();

        return [
            'message' => 'Logged out',
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $user = User::find($id);
        $username = $request->input('username');
        if(!empty($username)) {
            $user->username = $username;
        } 
        $sex = $request->input('sex');
        if(!empty($sex)) {
            $user->sex = $sex;
        }
        $dob = $request->input('dob');
        if(!empty($dob)) {
            $savedDate = DateTime::createFromFormat('Y-m-d', $dob);
            $user->dob = $savedDate;
        }
        
        $user->save();
        return response(['message' => 'user updated successfully'], 201);
    }

    private function getTokenId($tokenToCheck) {
        return substr($tokenToCheck, 0, strpos($tokenToCheck, '|'));
    }

    private function getCard($user) {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $pms = $stripe->customers->allPaymentMethods(
            $user['stripe_id'],
            ['type' => 'card']
        );
        if($pms && sizeof($pms) > 0) {
            $cardFromStripe = $pms['data'][0]['card'];
            $cardResponse = [
                'cardNumber' => $cardFromStripe['last4'],
                'effDate' => $cardFromStripe['exp_month'].'/'.$cardFromStripe['exp_year'],
            ];
            $user->card = $cardResponse;
        }
    }
}
