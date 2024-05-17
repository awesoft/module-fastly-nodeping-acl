<?php

declare(strict_types=1);

namespace Awesoft\FastlyNodepingAcl\Test\Unit\Model\Config;

use Awesoft\FastlyNodepingAcl\Model\Config\Source\IpType;
use PHPUnit\Framework\TestCase;

class IpTypeTest extends TestCase
{
    /**
     * @return void
     */
    public function testToOptionArray(): void
    {
        $this->assertSame(
            [
                ['value' => IpType::ANY, 'label' => 'IPv4 & IPv6'],
                ['value' => IpType::IPV4, 'label' => 'IPv4 only'],
                ['value' => IpType::IPV6, 'label' => 'IPv6 only'],
            ],
            (new IpType())->toOptionArray(),
        );
    }
}
