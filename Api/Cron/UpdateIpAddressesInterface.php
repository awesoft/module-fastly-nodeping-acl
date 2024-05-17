<?php

declare(strict_types=1);

namespace Awesoft\FastlyNodepingAcl\Api\Cron;

interface UpdateIpAddressesInterface
{
    /**
     * @return void
     */
    public function execute(): void;
}
