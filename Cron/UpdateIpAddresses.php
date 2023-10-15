<?php

declare(strict_types=1);

namespace Awesoft\FastlyNodepingAcl\Cron;

use Awesoft\FastlyNodepingAcl\Model\Config;
use Awesoft\FastlyNodepingAcl\Model\FastlyApi;
use Awesoft\FastlyNodepingAcl\Model\NodepingApi;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class UpdateIpAddresses
{
    /**
     * UpdateIpAddressList constructor.
     *
     * @param LoggerInterface $logger
     * @param NodepingApi $nodepingApi
     * @param FastlyApi $fastlyApi
     * @param Config $config
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly NodepingApi $nodepingApi,
        private readonly FastlyApi $fastlyApi,
        private readonly Config $config,
    ) {
    }

    /**
     * Execute cron to update IP addresses
     *
     * @return void
     * @throws LocalizedException
     * @throws GuzzleException
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
