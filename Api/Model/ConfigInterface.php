<?php

namespace Awesoft\FastlyNodepingAcl\Api\Model;

interface ConfigInterface
{
    /** @const string XML_PATH_IS_ENABLED */
    public const XML_PATH_IS_ENABLED = 'awesoft/fastly_nodeping_acl/is_enabled';

    /** @const string XML_PATH_IP_TYPE */
    public const XML_PATH_IP_TYPE = 'awesoft/fastly_nodeping_acl/ip_type';

    /** @const string XML_PATH_FASTLY_ACL */
    public const XML_PATH_FASTLY_ACL = 'awesoft/fastly_nodeping_acl/fastly_acl';

    /**
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * @return bool
     */
    public function isFullyEnabled(): bool;

    /**
     * @return string
     */
    public function getIpType(): string;

    /**
     * @return int
     */
    public function getIpTypeFilterOption(): int;

    /**
     * @return string
     */
    public function getFastlyAcl(): string;
}
