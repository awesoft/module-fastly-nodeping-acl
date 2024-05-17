<?php

declare(strict_types=1);

namespace Awesoft\FastlyNodepingAcl\Model;

use Awesoft\FastlyNodepingAcl\Api\Model\FastlyApiInterface;
use Fastly\Cdn\Helper\Vcl;
use Fastly\Cdn\Model\Api;
use Magento\Framework\Exception\LocalizedException;

class FastlyApi implements FastlyApiInterface
{
    /**
     * FastlyApi constructor.
     *
     * @param Api $api
     * @param Vcl $vcl
     */
    public function __construct(
        private readonly Api $api,
        private readonly Vcl $vcl,
    ) {
    }

    /**
     * Get ACL ID
     *
     * @param string $aclName
     * @return string
     * @throws LocalizedException
     */
    public function getAclId(string $aclName): string
    {
        $service = $this->api->checkServiceDetails();
        $currActiveVersion = $this->vcl->getCurrentVersion($service->versions);

        $acl = $this->api->getSingleAcl($currActiveVersion, $aclName);
        if (!$acl || !isset($acl->id)) {
            throw new LocalizedException(__('The ACL with name "%1" is not found.', $aclName));
        }

        return (string)$acl->id;
    }

    /**
     * Get ACL entries
     *
     * @param string $aclId
     * @return array
     * @throws LocalizedException
     */
    public function getAclItems(string $aclId): array
    {
        $aclItems = (array)$this->api->aclItemsList($aclId);
        if (empty($aclItems)) {
            return [];
        }

        $items = [];
        foreach ($aclItems as $aclItem) {
            $items[$aclItem->ip] = $aclItem->id;
        }

        return $items;
    }

    /**
     * Create an ACL item
     *
     * @param string $aclId
     * @param string $ip
     * @param string $comment
     * @return void
     * @throws LocalizedException
     */
    public function createAclItem(string $aclId, string $ip, string $comment = ''): void
    {
        $this->api->upsertAclItem($aclId, $ip, 0, $comment);
    }

    /**
     * Delete ACL item
     *
     * @param string $aclId
     * @param string $aclItemId
     * @return void
     * @throws LocalizedException
     */
    public function deleteAclItem(string $aclId, string $aclItemId): void
    {
        $this->api->deleteAclItem($aclId, $aclItemId);
    }
}
