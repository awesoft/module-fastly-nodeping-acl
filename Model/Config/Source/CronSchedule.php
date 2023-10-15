<?php

declare(strict_types=1);

namespace Awesoft\FastlyNodepingAcl\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CronSchedule implements OptionSourceInterface
{
    public const DAILY = '0 0 * * *';
    public const WEEKLY = '0 0 * * 0';
    public const MONTHLY = '0 0 1 * *';

    /**
     * Cron schedule options list
     *
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::DAILY, 'label' => 'Daily'],
            ['value' => self::WEEKLY, 'label' => 'Weekly'],
            ['value' => self::MONTHLY, 'label' => 'Monthly'],
        ];
    }
}
