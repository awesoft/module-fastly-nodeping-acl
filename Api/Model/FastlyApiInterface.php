<?php

namespace Awesoft\FastlyNodepingAcl\Api\Model;

interface FastlyApiInterface
{
    /**
     * @param string $aclName
     * @return string
     */
    public function getAclId(string $aclName): string;

    /**
     * @param string $aclId
     * @return array
     */
    public function getAclItems(string $aclId): array;

    /**
     * @param string $aclId
     * @param string $ip
     * @param string $comment
     * @return void
     */
    public function createAclItem(string $aclId, string $ip, string $comment = ''): void;

    /**
     * @param string $aclId
     * @param string $aclItemId
     * @return void
     */
    public function deleteAclItem(string $aclId, string $aclItemId): void;
}
