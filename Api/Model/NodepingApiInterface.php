<?php

namespace Awesoft\FastlyNodepingAcl\Api\Model;

use GuzzleHttp\RequestOptions;

interface NodepingApiInterface
{
    /** @const string ENDPOINT */
    public const ENDPOINT = 'https://nodeping.com/content/txt/pinghosts.txt';

    /** @const array OPTIONS */
    public const OPTIONS = [
        RequestOptions::HEADERS => ['Cache-Control' => 'no-cache, no-store'],
        RequestOptions::CONNECT_TIMEOUT => 5,
        RequestOptions::READ_TIMEOUT => 5,
        RequestOptions::VERIFY => true,
    ];

    /**
     * @return array
     */
    public function getIpAddresses(): array;
}
