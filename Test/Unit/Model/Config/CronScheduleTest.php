<?php

declare(strict_types=1);

namespace Awesoft\FastlyNodepingAcl\Test\Unit\Model\Config;

use Awesoft\FastlyNodepingAcl\Model\Config\Source\CronSchedule;
use PHPUnit\Framework\TestCase;

class CronScheduleTest extends TestCase
{
    /**
     * @return void
     */
    public function testToOptionArray(): void
    {
        $this->assertSame(
            [
                ['value' => CronSchedule::DAILY, 'label' => 'Daily'],
                ['value' => CronSchedule::WEEKLY, 'label' => 'Weekly'],
                ['value' => CronSchedule::MONTHLY, 'label' => 'Monthly'],
            ],
            (new CronSchedule())->toOptionArray(),
        );
    }
}
