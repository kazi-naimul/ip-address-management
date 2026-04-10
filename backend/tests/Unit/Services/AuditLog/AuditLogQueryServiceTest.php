<?php

namespace Tests\Unit\Services\AuditLog;

use App\Models\IpAddress;
use App\Repositories\AuditLogRepository;
use App\Repositories\IpAddressRepository;
use App\Services\AuditLog\AuditLogQueryService;
use PHPUnit\Framework\TestCase;
use Mockery;
use Mockery\MockInterface;

class AuditLogQueryServiceTest extends TestCase
{
    private AuditLogQueryService $auditLogQueryService;
    private MockInterface $auditLogRepository;
    private MockInterface $ipAddressRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->auditLogRepository = Mockery::mock(AuditLogRepository::class);
        $this->ipAddressRepository = Mockery::mock(IpAddressRepository::class);
        $this->auditLogQueryService = new AuditLogQueryService(
            $this->auditLogRepository,
            $this->ipAddressRepository,
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_audit_logs()
    {
        $logs = collect(['log1', 'log2']);

        $this->auditLogRepository
            ->shouldReceive('getAllAuditLogs')
            ->once()
            ->with(50)
            ->andReturn($logs);

        $result = $this->auditLogQueryService->getAllAuditLogs();

        $this->assertTrue($result['status']);
        $this->assertEquals(200, $result['code']);
        $this->assertEquals('Audit logs retrieved successfully', $result['message']);
        $this->assertEquals($logs, $result['data']);
    }

    public function test_get_login_audit_logs()
    {
        $logs = collect(['login1', 'login2']);

        $this->auditLogRepository
            ->shouldReceive('getLoginAuditLogs')
            ->once()
            ->with(null, 50)
            ->andReturn($logs);

        $result = $this->auditLogQueryService->getLoginAuditLogs();

        $this->assertTrue($result['status']);
        $this->assertEquals(200, $result['code']);
        $this->assertEquals('Login audit logs retrieved successfully', $result['message']);
        $this->assertEquals($logs, $result['data']);
    }

    public function test_get_user_login_audit_logs()
    {
        $userId = 1;
        $logs = collect(['user_login1', 'user_login2']);

        $this->auditLogRepository
            ->shouldReceive('getLoginAuditLogs')
            ->once()
            ->with($userId, 50)
            ->andReturn($logs);

        $result = $this->auditLogQueryService->getUserLoginAuditLogs($userId);

        $this->assertTrue($result['status']);
        $this->assertEquals(200, $result['code']);
        $this->assertEquals('User login history retrieved successfully', $result['message']);
        $this->assertEquals($logs, $result['data']);
    }

    public function test_get_ip_address_change_history_successful()
    {
        $ipAddress = new IpAddress(['ip_address' => '192.168.1.1', 'label' => 'Test IP']);
        $history = collect(['change1', 'change2']);

        $this->ipAddressRepository
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($ipAddress);

        $this->auditLogRepository
            ->shouldReceive('getAuditLogsByModelId')
            ->once()
            ->with(IpAddress::class, 1)
            ->andReturn($history);

        $result = $this->auditLogQueryService->getIpAddressChangeHistory(1);

        $this->assertTrue($result['status']);
        $this->assertEquals(200, $result['code']);
        $this->assertEquals('IP address change history retrieved successfully', $result['message']);
        $this->assertArrayHasKey('ip_address', $result['data']);
        $this->assertArrayHasKey('changes', $result['data']);
        $this->assertEquals($history, $result['data']['changes']);
    }

    public function test_get_ip_address_change_history_not_found()
    {
        $this->ipAddressRepository
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        $result = $this->auditLogQueryService->getIpAddressChangeHistory(999);

        $this->assertFalse($result['status']);
        $this->assertEquals(404, $result['code']);
        $this->assertEquals('IP address not found', $result['message']);
    }
}
