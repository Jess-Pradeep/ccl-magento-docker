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
 * @package    Ca
 * @version    1.12.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model\Company;

use Aheadworks\Ca\Model\Customer\CompanyUser\CompanyAdmin\AssignmentProcessorInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Aheadworks\StoreCredit\Api\Data\TransactionInterface;

class AssignmentProcessor implements AssignmentProcessorInterface
{
    /**
     * @param Manager $thirdPartyModuleManager
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        private readonly Manager $thirdPartyModuleManager,
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    /**
     * Move credit limit data from old company admin to new company admin
     *
     * @param CustomerInterface $oldCompanyAdmin
     * @param CustomerInterface $newCompanyAdmin
     * @return bool
     * @throws LocalizedException
     */
    public function process(CustomerInterface $oldCompanyAdmin, CustomerInterface $newCompanyAdmin): bool
    {
        if (!$this->thirdPartyModuleManager->isAwStoreCreditModuleEnabled()) {
            return false;
        }

        /** @var \Aheadworks\StoreCredit\Api\SummaryRepositoryInterface $summaryRepository */
        $summaryRepository = $this->objectManager->get(
            \Aheadworks\StoreCredit\Api\SummaryRepositoryInterface::class
        );

        try {
            $newAdminSummary = $summaryRepository->get($newCompanyAdmin->getId());
            if ($newAdminSummary) {
                throw new LocalizedException(__('Provided user has its own store credit history'));
            }
            // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
        } catch (NoSuchEntityException $exception) {
            // nothing happens
        }

        try {
            $oldAdminSummary = $summaryRepository->get($oldCompanyAdmin->getId());
            $oldAdminSummary->setCustomerId($newCompanyAdmin->getId());
            $summaryRepository->save($oldAdminSummary);

            $transactionSource = $this->objectManager->get(
                \Aheadworks\StoreCredit\Model\ResourceModel\Transaction::class
            );

            $transactionSource
                ->getConnection()
                ->update(
                    $transactionSource->getMainTable(),
                    [TransactionInterface::CUSTOMER_ID => $newCompanyAdmin->getId()],
                    TransactionInterface::CUSTOMER_ID . ' = ' . $oldCompanyAdmin->getId()
                );

            // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
        } catch (NoSuchEntityException $exception) {
            // nothing happens
        }

        return true;
    }
}
