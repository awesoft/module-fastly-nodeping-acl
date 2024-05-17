<?php

declare(strict_types=1);

namespace Awesoft\FastlyNodepingAcl\Model;

use Awesoft\FastlyNodepingAcl\Api\Model\ConfigInterface;
use Awesoft\FastlyNodepingAcl\Api\Model\NodepingApiInterface;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;

class NodepingApi implements NodepingApiInterface
{
    /**
     * NodepingApi constructor.
     *
     * @param ClientFactory $clientFactory
     * @param ConfigInterface $config
     */
    public function __construct(
        private readonly ClientFactory $clientFactory,
        private readonly ConfigInterface $config,
    ) {
    }

    /**
     * Get IP addresses
     *
     * @return array
     * @throws GuzzleException
     */
    public function getIpAddresses(): array
    {
        $contents = $this->clientFactory->create()->get(self::ENDPOINT, self::OPTIONS)->getBody()->getContents();
        $filterOption = $this->config->getIpTypeFilterOption();
        $lines = explode(PHP_EOL, trim($contents));
        $ips = [];

        foreach ($lines as $line) {
            [$host, $ip] = explode(' ', $line, 2);
            if (filter_var($ip, FILTER_VALIDATE_IP, $filterOption) !== false) {
                $ips[$ip] = $host;
            }
        }

        return $ips;
    }
}
