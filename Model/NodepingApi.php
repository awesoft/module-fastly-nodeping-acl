<?php

declare(strict_types=1);

namespace Awesoft\FastlyNodepingAcl\Model;

use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class NodepingApi
{
    private const ENDPOINT = 'https://nodeping.com/content/txt/pinghosts.txt';
    private const OPTIONS = [
        RequestOptions::HEADERS => ['Cache-Control' => 'no-cache, no-store'],
        RequestOptions::CONNECT_TIMEOUT => 5,
        RequestOptions::READ_TIMEOUT => 5,
        RequestOptions::VERIFY => true,
    ];

    /**
     * NodepingApi constructor.
     *
     * @param ClientFactory $clientFactory
     * @param Config $config
     */
    public function __construct(
        private readonly ClientFactory $clientFactory,
        private readonly Config $config,
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
