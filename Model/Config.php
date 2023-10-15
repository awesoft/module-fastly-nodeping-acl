<?php

declare(strict_types=1);

namespace Awesoft\FastlyNodepingAcl\Model;

use Awesoft\FastlyNodepingAcl\Model\Config\Source\IpType;
use Fastly\Cdn\Model\Config as FastlyConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    public const XML_PATH_IS_ENABLED = 'awesoft/fastly_nodeping_acl/is_enabled';
    public const XML_PATH_IP_TYPE = 'awesoft/fastly_nodeping_acl/ip_type';
    public const XML_PATH_FASTLY_ACL = 'awesoft/fastly_nodeping_acl/fastly_acl';

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param FastlyConfig $fastlyConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly FastlyConfig $fastlyConfig,
    ) {
    }

    /**
     * Is module enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_IS_ENABLED);
    }

    /**
     * Is module enabled together with Fastly
     *
     * @return bool
     */
    public function isFullyEnabled(): bool
    {
        return $this->isEnabled() && $this->fastlyConfig->isFastlyEnabled();
    }

    /**
     * Get selected IP type
     *
     * @return string
     */
    public function getIpType(): string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_IP_TYPE) ?: IpType::ANY;
    }

    /**
     * Get IP filter type
     *
     * @return int
     */
    public function getIpTypeFilterOption(): int
    {
        return match ($this->getIpType()) {
            IpType::IPV4 => FILTER_FLAG_IPV4,
            IpType::IPV6 => FILTER_FLAG_IPV6,
            default => 0,
        };
    }

    /**
     * Get selected Fastly ACL
     *
     * @return string
     */
    public function getFastlyAcl(): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_FASTLY_ACL);
    }
}
