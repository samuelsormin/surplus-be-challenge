<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PassportAuthController extends BaseController
{
    public function __construct()
    {
        //
    }

    /**
     * Register user
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        // validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:4',
            'email' => 'required|email:rfc,dns',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            $errorMsg = implode(' | ', $validator->errors()->all());

            return $this->sendError($errorMsg);
        }

        // save user's data
        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ];

            $user = User::create($userData);

            $token = $user->createToken('SurplusPassportAuth')->accessToken;

            return $this->sendResponse($token, 'Register success.');
            //
        } catch (\Throwable $th) {
            Log::error('Failed register user', [
                'msg' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            return $this->sendError('Failed register user', 500);
        }
    }

    /**
     * Log in user
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        // validate request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            $errorMsg = implode(' | ', $validator->errors()->all());

            return $this->sendError($errorMsg);
        }

        $loginData = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        // log in user
        if (auth()->attempt($loginData)) {
            $token = auth()->user()->createToken('SurplusPassportAuth')->accessToken;

            return $this->sendResponse($token, 'Logged in.');
        }

        return $this->sendError('Log in failed.');
    }
}
