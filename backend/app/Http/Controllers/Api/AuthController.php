<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Auth\LoginService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private LoginService $loginService)
    {
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $response = $this->loginService->login($credentials, $request->ip(), $request->userAgent() ?? '');

        return response()->json($response, $response['code']);
    }
}
