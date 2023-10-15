<?php

declare(strict_types=1);

namespace Awesoft\FastlyNodepingAcl\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class IpType implements OptionSourceInterface
{
    public const ANY = 'any';
    public const IPV4 = 'ipv4';
    public const IPV6 = 'ipv6';

    /**
     * Ip type options list
     *
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::ANY, 'label' => 'IPv4 & IPv6'],
            ['value' => self::IPV4, 'label' => 'IPv4 only'],
            ['value' => self::IPV6, 'label' => 'IPv6 only'],
        ];
    }
}
