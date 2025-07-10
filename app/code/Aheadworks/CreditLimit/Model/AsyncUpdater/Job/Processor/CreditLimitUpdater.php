<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Model\AsyncUpdater\Job\Processor;

use Aheadworks\CreditLimit\Model\AsyncUpdater\Job\ProcessorInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface as ParamsInterface;
use Aheadworks\CreditLimit\Model\Customer\SummaryLoader;
use Aheadworks\CreditLimit\Api\CreditLimitManagementInterface;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Model\ResourceModel\CustomerGroupConfig as CustomerGroupConfigResource;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\CreditLimit\Model\Service\TransactionService;

/**
 * Class CreditLimitUpdater
 *
 * @package Aheadworks\CreditLimit\Model\AsyncUpdater\Job\Processor
 */
class CreditLimitUpdater implements ProcessorInterface
{
    /**
     * @var SummaryLoader
     */
    private $summaryLoader;

    /**
     * @var CreditLimitManagementInterface
     */
    private $creditLimitManagement;

    /**
     * @var CustomerGroupConfigResource
     */
    private $customerGroupConfigResource;

    /**
     * @var TransactionService
     */
    private $transactionService;

    /**
     * CreditLimitUpdater constructor.
     * @param CreditLimitManagementInterface $creditLimitManagement
     * @param SummaryLoader $summaryLoader
     * @param CustomerGroupConfigResource $customerGroupConfigResource
     * @param TransactionService $transactionService
     */
    public function __construct(
        CreditLimitManagementInterface $creditLimitManagement,
        SummaryLoader $summaryLoader,
        CustomerGroupConfigResource $customerGroupConfigResource,
        TransactionService $transactionService
    ) {
        $this->creditLimitManagement = $creditLimitManagement;
        $this->summaryLoader = $summaryLoader;
        $this->customerGroupConfigResource = $customerGroupConfigResource;
        $this->transactionService = $transactionService;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function process($configuration)
    {
        if (!$configuration[SummaryInterface::CREDIT_LIMIT]) {
            $this->customerGroupConfigResource->removeConfigValue(
                $configuration['customer_group_id'],
                $configuration[SummaryInterface::WEBSITE_ID]
            );
        } else {
            $this->customerGroupConfigResource->saveConfigValue(
                $configuration,
                $configuration[SummaryInterface::WEBSITE_ID]
            );
        }

        $summaryList = $this->summaryLoader->loadByCustomerGroupId($configuration['customer_group_id']);
        $transactionsParams = [];
        foreach ($summaryList as $summary) {
            if ($summary->getCompanyId()
                || $this->isWebsiteValueConfigured($configuration, $summary->getWebsiteId())
            ) {
                continue;
            }

            $transactionsParams[] = $this->creditLimitManagement->getTransactionParams(
                (int)$summary->getCustomerId(),
                (float)$configuration[SummaryInterface::CREDIT_LIMIT],
                '',
                '',
                $summary,
                $configuration['customer_group_id']
            );
        }
        if ($transactionsParams) {
            $this->transactionService->createMultipleTransaction($transactionsParams);
        }

        return true;
    }

    /**
     * Is website value configured
     *
     * @param array $configuration
     * @param int $customerWebsiteId
     * @return bool
     * @throws LocalizedException
     */
    private function isWebsiteValueConfigured($configuration, $customerWebsiteId)
    {
        if ($configuration[SummaryInterface::WEBSITE_ID] == 0) {
            return $this->customerGroupConfigResource->hasConfigValueForCustomerGroup(
                $configuration['customer_group_id'],
                $customerWebsiteId
            );
        }

        return false;
    }
}
