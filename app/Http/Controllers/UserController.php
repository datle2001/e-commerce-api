<?php

namespace App\Http\Controllers;

use App\DTOs\UserDTO;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Validator;
use App\Services\UserService;
use App\Services\SharedService;

class UserController extends Controller
{
    private UserService $userService;
    private SharedService $sharedService;

    public function __construct(UserService $userService, SharedService $sharedService)
    {
        $this->userService = $userService;
        $this->sharedService = $sharedService;
    }

    public function show()
    {
        return response(auth('sanctum')->user(), 201);
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'email' => 'required|string|unique:users|email',
        ], [
            'email' => 'The :attribute must be a valid email address.',
            'unique' => 'The :attribute has already been taken.'
        ]);

        if ($validator->fails()) {
            $message = $validator->getMessageBag()->first();
            return response(['message' => $message], 422);
        }

        $fields = $validator->validated();
        
        $response = $this->sharedService->verifyEmail($fields['email']);

        if ($response['error']) {
            return response()->json(['message' => 'We cannot verify your email right now. Please retry signing up later or call our support team.'], 401);
        }

        if ($response['response']->status == 'valid') {
            $userDTO = new UserDTO(
                $request->firstName, 
                $request->lastName, 
                $request->sex, 
                $request->dob, 
                $request->email, 
                $request->password
            );

            $token = $this->userService->createUser($userDTO);

            return response()->json($this->userService->createToken($token), 201);
        }

        if ($response['response']->status == 'invalid') {
            return response(['message' => 'We cannot verify your email. Please check your email address.'], 510);
        }

        return response(['message' => 'Contact Support Group'], 500);
    }
    public function loginWithToken(Request $request): JsonResponse
    {
        return response()->json($this->userService->createToken($request->bearerToken()), 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credValidator = Validator::make($request->all(), [
            'password' => 'required|string',
            'email' => 'required|string|email',
        ], []);

        if ($credValidator->fails()) {
            $message = $credValidator->getMessageBag()->first();
            return response()->json(['message' => $message], 422);
        }

        $fields = $credValidator->validated();
        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken($user->id)->plainTextToken;

        return response()->json($this->userService->createToken($token), 201);
    }

    public function logout(Request $request)
    {
        $user = auth('sanctum')->user();
        PersonalAccessToken::where('tokenable_id', $user->id)->delete();

        return response()->json([], 204);
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
        if (!empty($username)) {
            $user->username = $username;
        }
        $sex = $request->input('sex');
        if (!empty($sex)) {
            $user->sex = $sex;
        }
        $dob = $request->input('dob');
        if (!empty($dob)) {
            $savedDate = DateTime::createFromFormat('Y-m-d', $dob);
            $user->dob = $savedDate;
        }

        $user->save();
        return response(['message' => 'user updated successfully'], 201);
    }

    private function getTokenId($tokenToCheck)
    {
        return substr($tokenToCheck, 0, strpos($tokenToCheck, '|'));
    }

    private function getCard($user)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $pms = $stripe->customers->allPaymentMethods(
            $user['stripe_id'],
            ['type' => 'card']
        );
        if ($pms && sizeof($pms) > 0) {
            $cardFromStripe = $pms['data'][0]['card'];
            $cardResponse = [
                'cardNumber' => $cardFromStripe['last4'],
                'effDate' => $cardFromStripe['exp_month'] . '/' . $cardFromStripe['exp_year'],
            ];
            $user->card = $cardResponse;
        }
    }
}
