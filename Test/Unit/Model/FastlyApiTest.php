<?php

declare(strict_types=1);

namespace Awesoft\FastlyNodepingAcl\Test\Unit\Model;

use Awesoft\FastlyNodepingAcl\Model\FastlyApi;
use Fastly\Cdn\Helper\Vcl;
use Fastly\Cdn\Model\Api;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

class FastlyApiTest extends TestCase
{
    /** @var Api|MockObject $apiMock */
    private Api|MockObject $apiMock;

    /** @var Vcl|MockObject $vclMock */
    private Vcl|MockObject $vclMock;

    /** @var FastlyApi $fastlyApi */
    private FastlyApi $fastlyApi;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->apiMock = $this->createMock(Api::class);
        $this->vclMock = $this->createMock(Vcl::class);
        $this->fastlyApi = new FastlyApi($this->apiMock, $this->vclMock);
    }

    /**
     * @param array $data
     * @return stdClass
     */
    protected function createItem(array $data): stdClass
    {
        $item = new stdClass();
        foreach ($data as $key => $value) {
            $item->{$key} = $value;
        }

        return $item;
    }

    protected function setUpGetAclIdMocks(): void
    {
        $service = $this->createItem(['versions' => []]);
        $this->vclMock->expects($this->once())->method('getCurrentVersion')->willReturn('1');
        $this->apiMock->expects($this->once())->method('checkServiceDetails')->willReturn($service);
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testGetAclId(): void
    {
        $acl = $this->createItem(['id' => 'acl_id_1']);

        $this->setUpGetAclIdMocks();
        $this->apiMock->expects($this->once())->method('getSingleAcl')->willReturn($acl);

        $this->assertSame($acl->id, $this->fastlyApi->getAclId('nodeping_ips'));
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testGetAclIdNotFound(): void
    {
        $this->setUpGetAclIdMocks();
        $this->apiMock->expects($this->once())->method('getSingleAcl')->willReturn(null);
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('The ACL with name "nodeping_ips" is not found.');

        $this->fastlyApi->getAclId('nodeping_ips');
    }

    /**
     * @dataProvider aclItemsDataProvider
     * @param array $expected
     * @param array $items
     * @return void
     * @throws LocalizedException
     */
    public function testGetAclItems(array $expected, array $items): void
    {
        $this->apiMock->expects($this->once())->method('aclItemsList')->willReturn($items);

        $this->assertSame($expected, $this->fastlyApi->getAclItems('1'));
    }

    /**
     * @return array
     */
    public function aclItemsDataProvider(): array
    {
        $item1 = $this->createItem(['id' => '1', 'ip' => '192.168.1.1']);
        $item2 = $this->createItem(['id' => '2', 'ip' => '192.168.1.2']);

        return [
            [[], []],
            [
                [$item1->ip => $item1->id, $item2->ip => $item2->id],
                [$item1, $item2],
            ],
        ];
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testCreateAclItem(): void
    {
        $this->apiMock->expects($this->once())->method('upsertAclItem')->with('1', '192.168.1', 0, 'comment');

        $this->fastlyApi->createAclItem('1', '192.168.1', 'comment');
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testDeleteAclItem(): void
    {
        $this->apiMock->expects($this->once())->method('deleteAclItem')->with('1', 'item_1');

        $this->fastlyApi->deleteAclItem('1', 'item_1');
    }
}
