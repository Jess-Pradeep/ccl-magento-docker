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
 * @package    CtqGraphQl
 * @version    1.0.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CtqGraphQl\Model\Resolver\Mutation\Quote;

use Aheadworks\Ctq\Api\QuoteListManagementInterface;
use Aheadworks\Ctq\Model\Service\BuyerPermissionService;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteIdToMaskedQuoteIdInterface;

class CreateEmptyCartForCustomer
{
    /**
     * @param QuoteListManagementInterface $quoteListManagement
     * @param QuoteIdToMaskedQuoteIdInterface $quoteIdToMaskedQuoteId
     * @param BuyerPermissionService $buyerPermissionService
     * @param CustomerRepositoryInterface $customerRepository
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        private readonly QuoteListManagementInterface $quoteListManagement,
        private readonly QuoteIdToMaskedQuoteIdInterface $quoteIdToMaskedQuoteId,
        private readonly BuyerPermissionService $buyerPermissionService,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly CartRepositoryInterface $cartRepository
    ) {
    }

    /**
     * Create empty cart for customer
     *
     * @param int $customerId
     * @param int $storeId
     * @return string
     * @throws CouldNotSaveException
     * @throws GraphQlInputException
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute(int $customerId, int $storeId): string
    {
        $customer = $this->customerRepository->getById($customerId);
        if (!$this->buyerPermissionService->isAllowQuoteList((int)$customer->getGroupId(), $storeId)) {
            throw new GraphQlInputException(
                __('Cannot create cart on store for customer id="%customerId"', ['customerId' => $customerId])
            );
        }
        $quote = $this->quoteListManagement->getQuoteListForCustomer($customerId);
        $quoteId = $quote ? $quote->getId() : $this->quoteListManagement->createQuoteListForCustomer($customerId);
        $quote = $quote ?: $this->cartRepository->get($quoteId);
        $quote->setCustomerGroupId($customer->getGroupId());
        $this->cartRepository->save($quote);

        return $this->quoteIdToMaskedQuoteId->execute((int)$quoteId);
    }
}
