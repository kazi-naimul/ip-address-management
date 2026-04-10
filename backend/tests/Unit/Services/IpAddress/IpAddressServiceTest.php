<?php

namespace Tests\Unit\Services\IpAddress;

use App\Models\IpAddress;
use App\Models\User;
use App\Repositories\IpAddressRepository;
use App\Services\IpAddress\IpAddressService;
use PHPUnit\Framework\TestCase;
use Mockery;
use Mockery\MockInterface;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class IpAddressServiceTest extends TestCase
{
    private IpAddressService $ipAddressService;
    private MockInterface $ipAddressRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ipAddressRepository = Mockery::mock(IpAddressRepository::class);
        $this->ipAddressService = new IpAddressService($this->ipAddressRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_returns_ip_addresses()
    {
        // Arrange
        $ipAddresses = collect([
            new IpAddress(['ip_address' => '192.168.1.1', 'label' => 'Test IP']),
        ]);

        $this->ipAddressRepository
            ->shouldReceive('getAllWithCreator')
            ->once()
            ->andReturn($ipAddresses);

        // Act
        $result = $this->ipAddressService->getAll();

        // Assert
        $this->assertTrue($result['status']);
        $this->assertEquals(200, $result['code']);
        $this->assertEquals('IP addresses retrieved successfully', $result['message']);
        $this->assertEquals($ipAddresses, $result['data']);
    }

    public function test_create_ip_address_successfully()
    {
        // Arrange
        $user = new User();
        $user->id = 1;

        $data = [
            'ip_address' => '192.168.1.1',
            'label' => 'Test IP',
            'created_by' => $user->id,
        ];

        $ipAddress = new IpAddress($data);
        $ipAddress->id = 1;

        $this->ipAddressRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($ipAddress);

        Mockery::mock('alias:App\Services\AuditLog\AuditLogService')
            ->shouldReceive('record')
            ->once()
            ->with($user, 'ip_created', IpAddress::class, $ipAddress->id, $ipAddress->toArray());

        // Act
        $result = $this->ipAddressService->create($data, $user);

        // Assert
        $this->assertTrue($result['status']);
        $this->assertEquals(201, $result['code']);
        $this->assertEquals('IP address created successfully', $result['message']);
        $this->assertEquals($ipAddress, $result['data']);
    }

    public function test_update_ip_address_successfully()
    {
        // Arrange
        $user = new User();
        $user->id = 1;

        $ipAddress = new IpAddress([
            'ip_address' => '192.168.1.1',
            'label' => 'Old Label',
        ]);
        $ipAddress->id = 1;

        $data = ['label' => 'New Label'];

        $this->ipAddressRepository
            ->shouldReceive('update')
            ->once()
            ->with($ipAddress, ['label' => 'New Label'])
            ->andReturn(true);

        Mockery::mock('alias:App\Services\AuditLog\AuditLogService')
            ->shouldReceive('record')
            ->once()
            ->with($user, 'ip_updated', IpAddress::class, $ipAddress->id, Mockery::type('array'));

        // Act
        $result = $this->ipAddressService->update($ipAddress, $data, $user);

        // Assert
        $this->assertTrue($result['status']);
        $this->assertEquals(200, $result['code']);
        $this->assertEquals('IP address updated successfully', $result['message']);
        $this->assertEquals($ipAddress, $result['data']);
    }

    public function test_find_by_id_returns_ip_address()
    {
        // Arrange
        $ipAddress = new IpAddress(['ip_address' => '192.168.1.1', 'label' => 'Test IP']);
        $ipAddress->id = 1;

        $this->ipAddressRepository
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($ipAddress);

        // Act
        $result = $this->ipAddressService->findById(1);

        // Assert
        $this->assertEquals($ipAddress, $result);
    }

    public function test_find_by_id_returns_null_when_not_found()
    {
        // Arrange
        $this->ipAddressRepository
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        // Act
        $result = $this->ipAddressService->findById(999);

        // Assert
        $this->assertNull($result);
    }

    public function test_get_by_id_returns_ip_address()
    {
        // Arrange
        $ipAddress = Mockery::mock(IpAddress::class)->makePartial();
        $ipAddress->id = 1;
        $ipAddress->ip_address = '192.168.1.1';
        $ipAddress->label = 'Test IP';
        $ipAddress->shouldReceive('load')->with('creator')->andReturnSelf();

        $this->ipAddressRepository
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($ipAddress);

        // Act
        $result = $this->ipAddressService->getById(1);

        // Assert
        $this->assertTrue($result['status']);
        $this->assertEquals(200, $result['code']);
        $this->assertEquals('IP address retrieved successfully', $result['message']);
        $this->assertEquals($ipAddress, $result['data']);
    }

    public function test_get_by_id_returns_not_found()
    {
        // Arrange
        $this->ipAddressRepository
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        // Act
        $result = $this->ipAddressService->getById(999);

        // Assert
        $this->assertFalse($result['status']);
        $this->assertEquals(404, $result['code']);
        $this->assertEquals('IP address not found', $result['message']);
    }

    public function test_update_by_id_successful()
    {
        // Arrange
        $user = new User();
        $user->id = 1;

        $ipAddress = new IpAddress([
            'ip_address' => '192.168.1.1',
            'label' => 'Old Label',
        ]);
        $ipAddress->id = 1;

        $data = ['label' => 'New Label'];

        $this->ipAddressRepository
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($ipAddress);

        $this->ipAddressRepository
            ->shouldReceive('update')
            ->once()
            ->with($ipAddress, ['label' => 'New Label'])
            ->andReturn(true);

        Mockery::mock('alias:App\Services\AuditLog\AuditLogService')
            ->shouldReceive('record')
            ->once()
            ->with($user, 'ip_updated', IpAddress::class, $ipAddress->id, Mockery::type('array'));

        // Act
        $result = $this->ipAddressService->updateById(1, $data, $user);

        // Assert
        $this->assertTrue($result['status']);
        $this->assertEquals(200, $result['code']);
        $this->assertEquals('IP address updated successfully', $result['message']);
    }

    public function test_update_by_id_not_found()
    {
        // Arrange
        $user = new User();
        $user->id = 1;

        $data = ['label' => 'New Label'];

        $this->ipAddressRepository
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturn(null);

        // Act
        $result = $this->ipAddressService->updateById(999, $data, $user);

        // Assert
        $this->assertFalse($result['status']);
        $this->assertEquals(404, $result['code']);
        $this->assertEquals('IP address not found', $result['message']);
    }
}
