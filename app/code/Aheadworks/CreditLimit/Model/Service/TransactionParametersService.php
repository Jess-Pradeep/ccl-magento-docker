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
declare(strict_types=1);

namespace Aheadworks\CreditLimit\Model\Service;

use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersExtensionFactory;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;

/**
 * Class TransactionParametersService
 */
class TransactionParametersService
{
    /**
     * @var TransactionParametersExtensionFactory
     */
    private $transactionParamsExtensionFactory;

    /**
     * TransactionParametersService constructor.
     *
     * @param TransactionParametersExtensionFactory $transactionParamsExtensionFactory
     */
    public function __construct(TransactionParametersExtensionFactory $transactionParamsExtensionFactory)
    {
        $this->transactionParamsExtensionFactory = $transactionParamsExtensionFactory;
    }

    /**
     * Set summary object to extension data
     *
     * @param TransactionParametersInterface $transactionParams
     * @param SummaryInterface $summary
     * @return void
     */
    public function setSummaryExtensionData(
        TransactionParametersInterface $transactionParams,
        SummaryInterface $summary
    ): void {
        $extension = $transactionParams->getExtensionAttributes();
        if (!$extension) {
            $extension = $this->transactionParamsExtensionFactory->create();
        }
        $extension->setSummaryObject($summary);
        $transactionParams->setExtensionAttributes($extension);
    }

    /**
     * Get summary object from extension data
     *
     * @param TransactionParametersInterface $transactionParams
     * @return SummaryInterface|null
     */
    public function getSummaryExtensionData(TransactionParametersInterface $transactionParams): ?SummaryInterface
    {
        $result = null;
        $extension = $transactionParams->getExtensionAttributes();
        if ($extension) {
            $result = $extension->getSummaryObject();
        }

        return $result;
    }

    /**
     * Set customer group id to extension data
     *
     * @param TransactionParametersInterface $transactionParams
     * @param int $customerGroupId
     * @return void
     */
    public function setCustomerGroupIdExtensionData(
        TransactionParametersInterface $transactionParams,
        int $customerGroupId
    ): void {
        $extension = $transactionParams->getExtensionAttributes();
        if (!$extension) {
            $extension = $this->transactionParamsExtensionFactory->create();
        }
        $extension->setCustomerGroupId($customerGroupId);
        $transactionParams->setExtensionAttributes($extension);
    }

    /**
     * Get customer group id from extension data
     *
     * @param TransactionParametersInterface $transactionParams
     * @return int|null
     */
    public function getCustomerGroupIdExtensionData(TransactionParametersInterface $transactionParams): ?int
    {
        $result = null;
        $extension = $transactionParams->getExtensionAttributes();
        if ($extension) {
            $result = $extension->getCustomerGroupId();
        }

        return $result;
    }
}
