<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Cron;

use Magento\Cron\Model\Schedule;
use Magento\Cron\Model\ScheduleFactory;
use Magento\Cron\Model\ResourceModel\Schedule as ResourceSchedule;
use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class ScheduleReAuthorization.
 */
class ScheduleReAuthorization
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    public function __construct(
        private readonly ScheduleFactory $scheduleFactory,
        private readonly ResourceSchedule $scheduleResourceModel,
        private readonly DateTime $dateTime
    ) {}

    /**
     * @param string|null $scheduleAt
     * @return void
     * @throws AlreadyExistsException
     */
    public function createSchedule(string $scheduleAt = null): void
    {
        if (is_null($scheduleAt)) {
            return;
        }

        $schedule = $this->scheduleFactory->create()
            ->setJobCode(ReAuthorizeByVirtualDevice::JOB_CODE)
            ->setStatus(Schedule::STATUS_PENDING)
            ->setCreatedAt(
                $this->dateTime->date(
                    self::DATE_FORMAT,
                    $this->dateTime->gmtTimestamp()
                )
            )
            ->setScheduledAt(
                $this->dateTime->date(
                    self::DATE_FORMAT,
                    $this->getMinusOneHour($scheduleAt)
                )
            );

        $this->scheduleResourceModel->save($schedule);
    }

    /**
     * @param string $dateString
     * @return string
     */
    private function getMinusOneHour(string $dateString): string
    {
        $timestamp = strtotime($dateString . ' -1 hour');
        return $this->dateTime->date(self::DATE_FORMAT, $timestamp);
    }
}
