<?php

declare(strict_types=1);

namespace Awesoft\FastlyNodepingAcl\Test\Unit\Cron;

use Awesoft\FastlyNodepingAcl\Cron\UpdateIpAddresses;
use Awesoft\FastlyNodepingAcl\Model\Config;
use Awesoft\FastlyNodepingAcl\Model\FastlyApi;
use Awesoft\FastlyNodepingAcl\Model\NodepingApi;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\TransferException;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class UpdateIpAddressListTest extends TestCase
{
    /** @var LoggerInterface|MockObject $loggerMock */
    private LoggerInterface|MockObject $loggerMock;

    /** @var NodepingApi|MockObject $nodepingApiMock */
    private NodepingApi|MockObject $nodepingApiMock;

    /** @var FastlyApi|MockObject $fastlyApiMock */
    private FastlyApi|MockObject $fastlyApiMock;

    /** @var Config|MockObject $configMock */
    private Config|MockObject $configMock;

    /** @var UpdateIpAddresses $updateIpAddressList */
    private UpdateIpAddresses $updateIpAddressList;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->nodepingApiMock = $this->createMock(NodepingApi::class);
        $this->fastlyApiMock = $this->createMock(FastlyApi::class);
        $this->configMock = $this->createMock(Config::class);
        $this->updateIpAddressList = new UpdateIpAddresses(
            $this->loggerMock,
            $this->nodepingApiMock,
            $this->fastlyApiMock,
            $this->configMock,
        );
    }

    /**
     * @param array $nodepingIps
     * @param array $aclItems
     * @return void
     */
    protected function createIpMocks(array $nodepingIps, array $aclItems): void
    {
        $this->configMock->expects($this->once())->method('isFullyEnabled')->willReturn(true);
        $this->configMock->expects($this->once())->method('getFastlyAcl')->willReturn('nodeping_ips');
        $this->fastlyApiMock->expects($this->once())->method('getAclId')->willReturn('aclId001');
        $this->fastlyApiMock->expects($this->once())->method('getAclItems')->willReturn($aclItems);
        $this->nodepingApiMock->expects($this->once())->method('getIpAddresses')->willReturn($nodepingIps);
    }

    /**
     * @return void
     * @throws GuzzleException
     * @throws LocalizedException
     */
    public function testExecuteNotFullyEnabled(): void
    {
        $this->configMock->expects($this->once())->method('isFullyEnabled')->willReturn(false);
        $this->configMock->expects($this->never())->method('getFastlyAcl');
        $this->loggerMock->expects($this->never())->method('info');
        $this->nodepingApiMock->expects($this->never())->method('getIpAddresses');
        $this->fastlyApiMock->expects($this->never())->method('getAclId');
        $this->fastlyApiMock->expects($this->never())->method('getAclItems');
        $this->fastlyApiMock->expects($this->never())->method('createAclItem');
        $this->fastlyApiMock->expects($this->never())->method('deleteAclItem');

        $this->updateIpAddressList->execute();
    }

    /**
     * @return void
     * @throws GuzzleException
     * @throws LocalizedException
     */
    public function testNodepingException(): void
    {
        $this->configMock->expects($this->once())->method('isFullyEnabled')->willReturn(true);
        $this->nodepingApiMock->expects($this->once())->method('getIpAddresses')->willThrowException(new TransferException());

        $this->expectException(TransferException::class);
        $this->updateIpAddressList->execute();
    }

    /**
     * @return void
     * @throws GuzzleException
     * @throws LocalizedException
     */
    public function testFastlyException(): void
    {
        $this->configMock->expects($this->once())->method('isFullyEnabled')->willReturn(true);
        $this->nodepingApiMock
            ->expects($this->once())
            ->method('getIpAddresses')
            ->willThrowException(new LocalizedException(__('error')));

        $this->expectException(LocalizedException::class);
        $this->updateIpAddressList->execute();
    }

    /**
     * @dataProvider ipMocksDataProvider
     * @param array $nodepingIps
     * @param array $aclItems
     * @param int $logger
     * @param int $create
     * @param int $delete
     * @return void
     * @throws GuzzleException
     * @throws LocalizedException
     */
    public function testExecute(array $nodepingIps, array $aclItems, int $logger, int $create, int $delete): void
    {
        $this->createIpMocks($nodepingIps, $aclItems);
        $this->loggerMock->expects($this->exactly($logger))->method('info');
        $this->fastlyApiMock->expects($this->exactly($create))->method('createAclItem');
        $this->fastlyApiMock->expects($this->exactly($delete))->method('deleteAclItem');

        $this->updateIpAddressList->execute();
    }

    /**
     * @return array[]
     */
    public function ipMocksDataProvider(): array
    {
        return [
            [
                [
                    '192.168.1.1' => 'pinghost1.local',
                    '192.168.1.2' => 'pinghost2.local',
                    '192.168.1.3' => 'pinghost3.local',
                    '192.168.1.4' => 'pinghost4.local',
                    '192.168.1.5' => 'pinghost5.local',
                ],
                [
                    '192.168.1.1' => 'acl_item_id_1',
                    '192.168.1.2' => 'acl_item_id_2',
                    '192.168.1.3' => 'acl_item_id_3',
                ],
                5,
                2,
                0,
            ],
            [
                [
                    '10.1.0.1' => 'pinghost1.local',
                    '10.1.0.2' => 'pinghost2.local',
                    '10.1.0.3' => 'pinghost3.local',
                    '10.1.0.4' => 'pinghost4.local',
                ],
                [
                    '10.1.0.1' => 'acl_item_id_1',
                    '10.1.0.2' => 'acl_item_id_2',
                    '10.1.0.3' => 'acl_item_id_3',
                    '10.1.0.4' => 'acl_item_id_4',
                    '10.1.0.5' => 'acl_item_id_5',
                ],
                5,
                0,
                1,
            ],
            [
                [
                    '172.50.10.1' => 'pinghost1.local',
                    '172.50.10.2' => 'pinghost2.local',
                    '172.50.10.4' => 'pinghost4.local',
                ],
                [
                    '172.50.10.1' => 'acl_item_id_1',
                    '172.50.10.3' => 'acl_item_id_3',
                    '172.50.10.4' => 'acl_item_id_4',
                    '172.50.10.5' => 'acl_item_id_5',
                ],
                5,
                1,
                2,
            ],
        ];
    }
}
