<?php
/**
 * Copyright © 2026 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Controller\Adminhtml\SubscriptionPlan;

use Alliance\AlliancePay\Service\Subscription\RepriceService;
use DateTimeImmutable;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Reprice.
 */
class Reprice extends Action
{
    public const ADMIN_RESOURCE = 'Alliance_AlliancePay::plan_reprice';

    public function __construct(
        Context $context,
        private readonly RepriceService $repriceService
    ) {
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $planId = (int)$this->getRequest()->getParam('id');
        $data = $this->getRequest()->getPostValue();

        try {
            $newRules = array_filter((array)($data['rules'] ?? []), 'is_array');
            $appliesFrom = new DateTimeImmutable((string)($data['applies_from'] ?? ''));

            $this->repriceService->reprice(
                $planId,
                $newRules,
                $appliesFrom,
                (string)($data['reason'] ?? ''),
                !empty($data['customers_notified_confirmed']),
                (int)$this->_auth->getUser()->getId()
            );

            $this->messageManager->addSuccessMessage(__('The subscription plan has been repriced.'));
        } catch (LocalizedException|\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('alliance_pay/subscriptionplan/edit', ['id' => $planId]);
    }
}
