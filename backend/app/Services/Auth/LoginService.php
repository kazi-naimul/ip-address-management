<?php

namespace App\Services\Auth;

use App\Library\Response\ResponseBuilder;
use App\Models\User;
use App\Services\AuditLog\AuditLogService;
use Illuminate\Support\Facades\Hash;

readonly class LoginService
{

    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return ResponseBuilder::getInstance()
                ->status(false)
                ->code(401)
                ->message('Invalid credentials')
                ->errors(['message' => 'Invalid credentials'])
                ->build();
        }

        $token = $user->createToken('api-token')->plainTextToken;

        AuditLogService::record($user, 'login', null, null, ['email' => $user->email]);

        return ResponseBuilder::getInstance()
            ->status(true)
            ->code(200)
            ->message('Login successful')
            ->data(['token' => $token])
            ->build();
    }

}
