<?php

namespace Tests\Unit\Services\Auth;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\Auth\LoginService;
use App\Services\AuditLog\AuditLogService;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\TestCase;
use Mockery;

class LoginServiceTest extends TestCase
{
    private LoginService $loginService;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepository::class);
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
            ->expects($this->once())
            ->method('findByEmail')
            ->with('test@example.com')
            ->willReturn($user);

        // Mock Hash::check
        Mockery::mock('alias:Illuminate\Support\Facades\Hash')
            ->shouldReceive('check')
            ->once()
            ->with('password123', 'hashedpassword')
            ->andReturn(true);

        // Mock request() helper
        $requestMock = Mockery::mock();
        $requestMock->shouldReceive('ip')->andReturn('127.0.0.1');
        $requestMock->shouldReceive('userAgent')->andReturn('TestAgent/1.0');
        
        // Mock the global request function
        $this->app = app(); // Get the Laravel app instance
        $this->app->instance('request', $requestMock);

        // Mock AuditLogService::record
        Mockery::mock('alias:App\Services\AuditLog\AuditLogService')
            ->shouldReceive('record')
            ->once()
            ->with($user, 'user_login', null, null, ['ip_address' => '127.0.0.1', 'user_agent' => 'TestAgent/1.0']);

        // Act
        $result = $this->loginService->login($credentials);

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
            ->expects($this->once())
            ->method('findByEmail')
            ->with('nonexistent@example.com')
            ->willReturn(null);

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
            ->expects($this->once())
            ->method('findByEmail')
            ->with('test@example.com')
            ->willReturn($user);

        // Mock Hash::check to return false
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