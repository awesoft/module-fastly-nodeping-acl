<?php

declare(strict_types=1);

namespace Awesoft\FastlyNodepingAcl\Cron;

use Awesoft\FastlyNodepingAcl\Api\Cron\UpdateIpAddressesInterface;
use Awesoft\FastlyNodepingAcl\Api\Model\ConfigInterface;
use Awesoft\FastlyNodepingAcl\Api\Model\FastlyApiInterface;
use Awesoft\FastlyNodepingAcl\Api\Model\NodepingApiInterface;
use Psr\Log\LoggerInterface;

class UpdateIpAddresses implements UpdateIpAddressesInterface
{
    /**
     * UpdateIpAddressList constructor.
     *
     * @param LoggerInterface $logger
     * @param NodepingApiInterface $nodepingApi
     * @param FastlyApiInterface $fastlyApi
     * @param ConfigInterface $config
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly NodepingApiInterface $nodepingApi,
        private readonly FastlyApiInterface $fastlyApi,
        private readonly ConfigInterface $config,
    ) {
    }

    /**
     * Execute cron to update IP addresses
     *
     * @return void
     */
    public function execute(): void
    {
        if (!$this->config->isFullyEnabled()) {
            return;
        }

        $ipAddresses = $this->nodepingApi->getIpAddresses();
        $fastlyAcl = $this->config->getFastlyAcl();
        $aclId = $this->fastlyApi->getAclId($fastlyAcl);
        $aclItems = $this->fastlyApi->getAclItems($aclId);

        foreach ($ipAddresses as $ip => $comment) {
            if (array_key_exists($ip, $aclItems)) {
                $this->logger->info('Nodeping IP already exists, skipping...', ['ip' => $ip]);
                unset($aclItems[$ip]);
                continue;
            }

            $this->fastlyApi->createAclItem($aclId, $ip, $comment);
            $this->logger->info('Added ACL item', ['ip' => $ip, 'comment' => $comment]);
            unset($aclItems[$ip]);
        }

        if (!empty($aclItems)) {
            foreach ($aclItems as $ip => $id) {
                $this->fastlyApi->deleteAclItem($aclId, $id);
                $this->logger->info('Removed ACL item', ['ip' => $ip, 'id' => $id]);
            }
        }
    }
}
