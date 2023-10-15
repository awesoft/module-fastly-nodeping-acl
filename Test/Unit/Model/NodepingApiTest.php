<?php

declare(strict_types=1);

namespace Awesoft\FastlyNodepingAcl\Test\Unit\Model;

use Awesoft\FastlyNodepingAcl\Model\Config;
use Awesoft\FastlyNodepingAcl\Model\NodepingApi;
use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class NodepingApiTest extends TestCase
{
    /** @var string $contents */
    private string $contents = PHP_EOL
        . 'pinghost1.local 154.74.35.179' . PHP_EOL
        . 'pinghost2.local 255.150.222.5' . PHP_EOL
        . 'pinghost3.local 39.73.113.221' . PHP_EOL
        . 'pinghost1.local 716a:3cfa:219f:a650:6525:7176:58f3:f0c1' . PHP_EOL
        . 'pinghost2.local 6b97:3865:e741:7c78:2277:d10c:0888:cd74' . PHP_EOL
        . 'pinghost3.local 288d:8f44:2391:f135:5242:e40f:f0e2:17b6' . PHP_EOL;

    /** @var Client|MockObject $clientMock */
    private Client|MockObject $clientMock;

    /** @var Config|MockObject $configMock */
    private Config|MockObject $configMock;

    /** @var NodepingApi $nodepingApi */
    private NodepingApi $nodepingApi;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(Client::class);
        $this->configMock = $this->createMock(Config::class);

        $clientFactoryMock = $this->createMock(ClientFactory::class);
        $clientFactoryMock->expects($this->once())->method('create')->willReturn($this->clientMock);

        $this->nodepingApi = new NodepingApi($clientFactoryMock, $this->configMock);
    }

    /**
     * @dataProvider ipAddressesDataProvider
     * @param array $expected
     * @param int $option
     * @return void
     * @throws GuzzleException
     */
    public function testGetIpAddresses(array $expected, int $option): void
    {
        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock->expects($this->once())->method('getContents')->willReturn($this->contents);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->expects($this->once())->method('getBody')->willReturn($streamMock);

        $this->clientMock->expects($this->once())->method('get')->willReturn($responseMock);
        $this->configMock->expects($this->once())->method('getIpTypeFilterOption')->willReturn($option);

        $this->assertSame($expected, $this->nodepingApi->getIpAddresses());
    }

    /**
     * @return array[]
     */
    public function ipAddressesDataProvider(): array
    {
        return [
            [
                [
                    '154.74.35.179' => 'pinghost1.local',
                    '255.150.222.5' => 'pinghost2.local',
                    '39.73.113.221' => 'pinghost3.local',
                ],
                FILTER_FLAG_IPV4,
            ],
            [
                [
                    '716a:3cfa:219f:a650:6525:7176:58f3:f0c1' => 'pinghost1.local',
                    '6b97:3865:e741:7c78:2277:d10c:0888:cd74' => 'pinghost2.local',
                    '288d:8f44:2391:f135:5242:e40f:f0e2:17b6' => 'pinghost3.local',
                ],
                FILTER_FLAG_IPV6,
            ],
            [
                [
                    '154.74.35.179' => 'pinghost1.local',
                    '255.150.222.5' => 'pinghost2.local',
                    '39.73.113.221' => 'pinghost3.local',
                    '716a:3cfa:219f:a650:6525:7176:58f3:f0c1' => 'pinghost1.local',
                    '6b97:3865:e741:7c78:2277:d10c:0888:cd74' => 'pinghost2.local',
                    '288d:8f44:2391:f135:5242:e40f:f0e2:17b6' => 'pinghost3.local',
                ],
                0,
            ]
        ];
    }
}
