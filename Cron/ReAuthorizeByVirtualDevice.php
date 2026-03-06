<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Cron;

use Alliance\AlliancePay\Service\Token\TokenAuthService;
use Exception;
use Alliance\AlliancePay\Logger\Logger;

/**
 * Class ReReauthorizeByVirtualDevice.
 */
class ReAuthorizeByVirtualDevice
{
    public const JOB_CODE = 'alliance_payment_reauthorize_token';

    public function __construct(
        private TokenAuthService $tokenAuthService,
        private Logger $logger
    ) {}

    /**
     * @return void
     */
    public function execute()
    {
        try {
            $authData = $this->tokenAuthService->authenticate();
            if ($authData->isAuthenticated()) {
                $this->logger->info(__('Token reauthorized successfully'));
            } else {
                $this->logger->error(__('Token reauthorization failed: ') . $authData->getMessage());
            }
        } catch (Exception $e) {
            $this->logger->error(__('Token reauthorization failed: ') . $e->getMessage());
        }
    }
}
