<?php

namespace App\Services\Auth;

use App\Library\Response\ResponseBuilder;
use App\Repositories\UserRepository;
use App\Services\AuditLog\AuditLogService;
use Illuminate\Support\Facades\Hash;

readonly class LoginService
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function login(array $credentials): array
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return ResponseBuilder::getInstance()
                ->status(false)
                ->code(401)
                ->message('Invalid credentials')
                ->errors(['message' => 'Invalid credentials'])
                ->build();
        }

        $token = $user->createToken('api-token')->plainTextToken;

        // Record login audit log
        AuditLogService::record(
            $user,
            'user_login',
            null,
            null,
            ['ip_address' => request()->ip(), 'user_agent' => request()->userAgent()]
        );

        return ResponseBuilder::getInstance()
            ->status(true)
            ->code(200)
            ->message('Login successful')
            ->data(['token' => $token])
            ->build();
    }

}
