<?php

declare(strict_types=1);

namespace Awesoft\FastlyNodepingAcl\Test\Unit\Model;

use Awesoft\FastlyNodepingAcl\Model\Config;
use Awesoft\FastlyNodepingAcl\Model\Config\Source\IpType;
use Fastly\Cdn\Model\Config as FastlyConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /** @var ScopeConfigInterface|MockObject $scopeConfigMock */
    private ScopeConfigInterface|MockObject $scopeConfigMock;

    /** @var FastlyConfig|MockObject $fastlyConfigMock */
    private FastlyConfig|MockObject $fastlyConfigMock;

    /** @var Config $config */
    private Config $config;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->fastlyConfigMock = $this->createMock(FastlyConfig::class);
        $this->config = new Config($this->scopeConfigMock, $this->fastlyConfigMock);
    }

    /**
     * @return void
     */
    public function testIsEnabled(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_IS_ENABLED)
            ->willReturn(false);

        $this->assertSame(false, $this->config->isEnabled());
    }

    /**
     * @return void
     */
    public function testIsFullyEnabled(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_IS_ENABLED)
            ->willReturn(true);

        $this->fastlyConfigMock->expects($this->once())
            ->method('isFastlyEnabled')
            ->willReturn(false);

        $this->assertSame(false, $this->config->isFullyEnabled());
    }

    /**
     * @return void
     */
    public function testGetIpType(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_IP_TYPE)
            ->willReturn(IpType::IPV4);

        $this->assertSame(IpType::IPV4, $this->config->getIpType());
    }

    /**
     * @return void
     */
    public function testGetIpTypeFilterOption(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_IP_TYPE)
            ->willReturn(IpType::IPV6);

        $this->assertSame(FILTER_FLAG_IPV6, $this->config->getIpTypeFilterOption());
    }

    /**
     * @return void
     */
    public function testGetFastlyAcl(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_FASTLY_ACL)
            ->willReturn('nodeping_ips');

        $this->assertSame('nodeping_ips', $this->config->getFastlyAcl());
    }
}
