<?php

namespace App\Http\Controllers\API;

use App\Enums\UserScope;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string'],
        ]);

        try {
            $data['password'] = Hash::make($data['password']);
            User::create($data);

            return new JsonResponse(['message' => 'Registration Successfull.'], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::channel('user')->critical($e->getMessage(), ['data' => $data, 'method' => __METHOD__]);
            return new JsonResponse(['message' => "We are unable to perform this task. Please try after sometime."], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string']
        ]);

        try {
            $response = Http::asForm()->post(env('PASSPORT_URL') . '/oauth/token', [
                'grant_type'    => 'password',
                'client_id'     => env('PASSPORT_PASSWORD_CLIENT_ID'),
                'client_secret' => env('PASSPORT_PASSWORD_CLIENT_SECRET'),
                'username'      => $data['email'],
                'password'      => $data['password'],
                'scope'         => User::where('email', $data['email'])->first()->type === UserType::ADMIN ? array_keys(UserScope::TOKENS_CAN)[1] : array_keys(UserScope::TOKENS_CAN)[0],
            ]);

            if ($response->failed()) {
                return new JsonResponse(['message' => 'Invalid Email and Password. Please try again'], Response::HTTP_BAD_REQUEST);
            }

            return new JsonResponse([
                'message' => 'login Successfull',
                'token'   => $response->json() ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::channel('user')->critical($e->getMessage(), ['data' => $data, 'method' => __METHOD__]);
            return new JsonResponse(['message' => "We are unable to perform this task. Please try after sometime."], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
