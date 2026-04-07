<?php

namespace Tests\Unit\Services\AuditLog;

use App\Models\IpAddress;
use App\Repositories\AuditLogRepository;
use App\Services\AuditLog\AuditLogQueryService;
use PHPUnit\Framework\TestCase;
use Mockery;

class AuditLogQueryServiceTest extends TestCase
{
    private AuditLogQueryService $auditLogQueryService;
    private AuditLogRepository $auditLogRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->auditLogRepository = $this->createMock(AuditLogRepository::class);
        $this->auditLogQueryService = new AuditLogQueryService($this->auditLogRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_audit_logs()
    {
        // Arrange
        $logs = collect(['log1', 'log2']);

        $this->auditLogRepository
            ->expects($this->once())
            ->method('getAllAuditLogs')
            ->with(50)
            ->willReturn($logs);

        // Act
        $result = $this->auditLogQueryService->getAllAuditLogs();

        // Assert
        $this->assertTrue($result['status']);
        $this->assertEquals(200, $result['code']);
        $this->assertEquals('Audit logs retrieved successfully', $result['message']);
        $this->assertEquals($logs, $result['data']);
    }

    public function test_get_login_audit_logs()
    {
        // Arrange
        $logs = collect(['login1', 'login2']);

        $this->auditLogRepository
            ->expects($this->once())
            ->method('getLoginAuditLogs')
            ->with(null, 50)
            ->willReturn($logs);

        // Act
        $result = $this->auditLogQueryService->getLoginAuditLogs();

        // Assert
        $this->assertTrue($result['status']);
        $this->assertEquals(200, $result['code']);
        $this->assertEquals('Login audit logs retrieved successfully', $result['message']);
        $this->assertEquals($logs, $result['data']);
    }

    public function test_get_user_login_audit_logs()
    {
        // Arrange
        $userId = 1;
        $logs = collect(['user_login1', 'user_login2']);

        $this->auditLogRepository
            ->expects($this->once())
            ->method('getLoginAuditLogs')
            ->with($userId, 50)
            ->willReturn($logs);

        // Act
        $result = $this->auditLogQueryService->getUserLoginAuditLogs($userId);

        // Assert
        $this->assertTrue($result['status']);
        $this->assertEquals(200, $result['code']);
        $this->assertEquals('User login history retrieved successfully', $result['message']);
        $this->assertEquals($logs, $result['data']);
    }

    public function test_get_ip_address_change_history_successful()
    {
        // Arrange
        $ipAddress = $this->createMock(IpAddress::class);
        $ipAddress->id = 1;
        $ipAddress->ip_address = '192.168.1.1';
        $ipAddress->label = 'Test IP';

        $history = collect(['change1', 'change2']);

        // Mock IpAddress::find
        Mockery::mock('overload:App\Models\IpAddress')
            ->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($ipAddress);

        $this->auditLogRepository
            ->expects($this->once())
            ->method('getAuditLogsByModelId')
            ->with(IpAddress::class, 1)
            ->willReturn($history);

        // Act
        $result = $this->auditLogQueryService->getIpAddressChangeHistory(1);

        // Assert
        $this->assertTrue($result['status']);
        $this->assertEquals(200, $result['code']);
        $this->assertEquals('IP address change history retrieved successfully', $result['message']);
        $this->assertArrayHasKey('ip_address', $result['data']);
        $this->assertArrayHasKey('changes', $result['data']);
        $this->assertEquals(1, $result['data']['ip_address']['id']);
        $this->assertEquals($history, $result['data']['changes']);
    }

    public function test_get_ip_address_change_history_not_found()
    {
        // Arrange
        $nonExistentId = 999;

        // Mock IpAddress::find to return null
        Mockery::mock('overload:App\Models\IpAddress')
            ->shouldReceive('find')
            ->once()
            ->with(999)
            ->andReturn(null);

        // Act
        $result = $this->auditLogQueryService->getIpAddressChangeHistory($nonExistentId);

        // Assert
        $this->assertFalse($result['status']);
        $this->assertEquals(404, $result['code']);
        $this->assertEquals('IP address not found', $result['message']);
    }
}