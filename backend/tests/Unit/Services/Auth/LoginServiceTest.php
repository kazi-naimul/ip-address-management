<?php

namespace Tests\Unit\Services\Auth;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\Auth\LoginService;
use PHPUnit\Framework\TestCase;
use Mockery;
use Mockery\MockInterface;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class LoginServiceTest extends TestCase
{
    private LoginService $loginService;
    private MockInterface $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->loginService = new LoginService($this->userRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_login_successful()
    {
        // Arrange
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->password = 'hashedpassword';
        $user->shouldReceive('createToken')->andReturn((object)['plainTextToken' => 'test-token']);

        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with('test@example.com')
            ->andReturn($user);

        Mockery::mock('alias:Illuminate\Support\Facades\Hash')
            ->shouldReceive('check')
            ->once()
            ->with('password123', 'hashedpassword')
            ->andReturn(true);

        Mockery::mock('alias:App\Services\AuditLog\AuditLogService')
            ->shouldReceive('record')
            ->once()
            ->with($user, 'user_login', null, null, ['ip_address' => '127.0.0.1', 'user_agent' => 'TestAgent/1.0']);

        // Act
        $result = $this->loginService->login($credentials, '127.0.0.1', 'TestAgent/1.0');

        // Assert
        $this->assertTrue($result['status']);
        $this->assertEquals(200, $result['code']);
        $this->assertEquals('Login successful', $result['message']);
        $this->assertArrayHasKey('token', $result['data']);
    }

    public function test_login_with_invalid_email()
    {
        // Arrange
        $credentials = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ];

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with('nonexistent@example.com')
            ->andReturn(null);

        // Act
        $result = $this->loginService->login($credentials);

        // Assert
        $this->assertFalse($result['status']);
        $this->assertEquals(401, $result['code']);
        $this->assertEquals('Invalid credentials', $result['message']);
        $this->assertArrayHasKey('errors', $result);
    }

    public function test_login_with_invalid_password()
    {
        // Arrange
        $user = Mockery::mock(User::class)->makePartial();
        $user->password = 'hashedpassword';

        $credentials = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ];

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->once()
            ->with('test@example.com')
            ->andReturn($user);

        Mockery::mock('alias:Illuminate\Support\Facades\Hash')
            ->shouldReceive('check')
            ->once()
            ->with('wrongpassword', 'hashedpassword')
            ->andReturn(false);

        // Act
        $result = $this->loginService->login($credentials);

        // Assert
        $this->assertFalse($result['status']);
        $this->assertEquals(401, $result['code']);
        $this->assertEquals('Invalid credentials', $result['message']);
        $this->assertArrayHasKey('errors', $result);
    }
}
